<?php

namespace App\Services;

use App\Models\InterventoModel;
use App\Models\TecnicoCompetenzaModel;
use App\Models\TipoInterventoModel;
use App\Models\VeicoloModel;

class AssegnazioneService
{
    private const PRIORITA_PESO = ['urgente' => 10, 'ordinario' => 5, 'programmato' => 1];

    public function proponi(string $data): array
    {
        $sedeLat = (float) setting('Azienda.sede_lat');
        $sedeLng = (float) setting('Azienda.sede_lng');

        if (! $sedeLat || ! $sedeLng) {
            return ['errore' => 'Coordinate sede non configurate. Vai in Impostazioni → Parametri Generali.'];
        }

        $giorno  = (int) date('N', strtotime($data));
        $tecnici = $this->getTecniciDisponibili($giorno);

        if (empty($tecnici)) {
            return ['errore' => 'Nessun tecnico disponibile per questa data.'];
        }

        $idTecnici   = array_column($tecnici, 'id');
        $competenze  = $this->getCompetenzePerTecnici($idTecnici);
        $tipiMap     = $this->getTipiMap();
        $veicoli     = (new VeicoloModel())->where('attivo', 1)->findAll();
        $veicoliCA   = array_values(array_filter($veicoli, fn($v) => $v['cambio_automatico']));
        $veicoliNorm = array_values(array_filter($veicoli, fn($v) => ! $v['cambio_automatico']));

        $tuttiInterventi = $this->getInterventiDaPianificare();
        $nonGeo     = array_values(array_filter($tuttiInterventi, fn($i) => ! $i['lat'] || ! $i['lng']));
        $interventi = array_values(array_filter($tuttiInterventi, fn($i) => $i['lat'] && $i['lng']));

        if (empty($interventi)) {
            return [
                'non_geocodificati' => $nonGeo,
                'non_pianificabili' => [],
                'proposte'          => [],
            ];
        }

        $jobs     = $this->buildJobs($interventi, $tipiMap);
        $vehicles = $this->buildVehicles($tecnici, $competenze, $sedeLat, $sedeLng);

        if (empty($vehicles)) {
            return ['errore' => 'Nessun tecnico con orari configurati correttamente.'];
        }

        $ors = (new VrpService())->ottimizza(['jobs' => $jobs, 'vehicles' => $vehicles]);

        if (isset($ors['errore'])) {
            return $ors;
        }

        return $this->parseRisposta($ors, $tecnici, $interventi, $competenze, $tipiMap, $veicoliCA, $veicoliNorm, $nonGeo);
    }

    // ── Raccolta dati ────────────────────────────────────────────────────────

    private function getTecniciDisponibili(int $giorno): array
    {
        // tecnici_orari non ha soft delete — query builder raw accettabile
        return db_connect()
            ->table('tecnici_orari o')
            ->select('u.id, u.nome, u.cognome, u.colore, u.richiede_cambio_auto,
                      o.ora_inizio, o.ora_fine, o.pausa_inizio, o.pausa_fine')
            ->join('users u', 'u.id = o.tecnico_id')
            ->where('o.giorno', $giorno)
            ->where('o.attivo', 1)
            ->where('u.ruolo', 'tecnico')
            ->get()->getResultArray();
    }

    private function getCompetenzePerTecnici(array $ids): array
    {
        if (empty($ids)) return [];

        // Usa il model — nessun soft delete su tecnici_competenze, ma coerenza con ORM
        $rows = (new TecnicoCompetenzaModel())->whereIn('tecnico_id', $ids)->findAll();

        // Returns [tecnico_id => [tipo_intervento_id => livello]]
        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r['tecnico_id']][(int) $r['tipo_intervento_id']] = (int) $r['livello'];
        }
        return $out;
    }

    private function getTipiMap(): array
    {
        // ['piscine' => ['id' => 1, 'durata_default' => 120], ...]
        $out = [];
        foreach ((new TipoInterventoModel())->where('attivo', 1)->findAll() as $t) {
            $out[$t['codice']] = ['id' => (int) $t['id'], 'durata_default' => (int) $t['durata_default']];
        }
        return $out;
    }

    private function getInterventiDaPianificare(): array
    {
        // InterventoModel ha useSoftDeletes = true → deleted_at IS NULL auto-applicato
        return (new InterventoModel())
            ->select('interventi.id, interventi.tipo_intervento, interventi.luogo_intervento,
                      interventi.descrizione, interventi.durata_viaggio, interventi.priorita,
                      interventi.fissato, interventi.ora_inizio,
                      c.lat, c.lng, c.tipo AS cliente_tipo,
                      c.ragsoc, c.cognome AS cliente_cognome, c.nome AS cliente_nome')
            ->join('clienti c', 'c.id = interventi.cliente_id AND c.deleted_at IS NULL')
            ->where('interventi.stato', 'da_pianificare')
            ->orderBy('FIELD(interventi.priorita, "urgente", "ordinario", "programmato")')
            ->findAll();
    }

    // ── Costruzione payload ORS ──────────────────────────────────────────────

    private function buildJobs(array $interventi, array $tipiMap): array
    {
        $jobs = [];
        foreach ($interventi as $i) {
            $tipo    = $tipiMap[$i['tipo_intervento']] ?? null;
            $durata  = (int) ($i['durata_viaggio'] ?: ($tipo['durata_default'] ?? 60));
            $service = $durata * 60;

            $job = [
                'id'          => (int) $i['id'],
                'description' => $this->nomeCliente($i),
                'location'    => [(float) $i['lng'], (float) $i['lat']],
                'service'     => $service,
                'priority'    => self::PRIORITA_PESO[$i['priorita']] ?? 5,
            ];

            if ($tipo) {
                $job['skills'] = [$tipo['id']];
            }

            if ($i['fissato'] && $i['ora_inizio']) {
                $sec = $this->timeToSeconds($i['ora_inizio']);
                $job['time_windows'] = [[$sec, $sec + $service + 1800]];
            }

            $jobs[] = $job;
        }
        return $jobs;
    }

    private function buildVehicles(array $tecnici, array $competenze, float $sedeLat, float $sedeLng): array
    {
        $vehicles = [];
        foreach ($tecnici as $t) {
            $twStart = $this->timeToSeconds($t['ora_inizio']);
            $twEnd   = $this->timeToSeconds($t['ora_fine']);

            // Skills ORS: solo livello >= 1 (livello 0 = non competente = fallback PHP)
            $skills = [];
            foreach ($competenze[$t['id']] ?? [] as $tipoId => $livello) {
                if ($livello >= 1) $skills[] = $tipoId;
            }

            $vehicle = [
                'id'          => (int) $t['id'],
                'description' => $t['cognome'] . ' ' . $t['nome'],
                'profile'     => 'driving-car',
                'start'       => [$sedeLng, $sedeLat],
                'end'         => [$sedeLng, $sedeLat],
                'time_window' => [$twStart, $twEnd],
                'skills'      => $skills,
            ];

            if ($t['pausa_inizio'] && $t['pausa_fine']) {
                $pStart = $this->timeToSeconds($t['pausa_inizio']);
                $pEnd   = $this->timeToSeconds($t['pausa_fine']);
                $vehicle['breaks'] = [[
                    'id'           => (int) $t['id'] * 1000,
                    'time_windows' => [[$pStart, $pEnd + 3600]],
                    'service'      => $pEnd - $pStart,
                ]];
            }

            $vehicles[] = $vehicle;
        }
        return $vehicles;
    }

    // ── Parsing risposta ORS + fallback livello 0 ────────────────────────────

    private function parseRisposta(array $ors, array $tecnici, array $interventi, array $competenze, array $tipiMap, array $veicoliCA, array $veicoliNorm, array $nonGeo): array
    {
        $tecniciMap    = array_column($tecnici, null, 'id');
        $interventiMap = array_column($interventi, null, 'id');
        $usatiCA       = 0;
        $proposte      = [];
        $assegnati     = [];

        foreach ($ors['routes'] ?? [] as $route) {
            $tecnico = $tecniciMap[$route['vehicle']] ?? null;
            if (! $tecnico) continue;

            if ($tecnico['richiede_cambio_auto']) {
                $veicolo = $veicoliCA[$usatiCA++] ?? null;
            } else {
                $idx     = count($proposte) - $usatiCA;
                $veicolo = $veicoliNorm[$idx] ?? null;
            }

            $tappe  = [];
            $ordine = 1;
            foreach ($route['steps'] as $step) {
                if ($step['type'] !== 'job') continue;
                $intervento = $interventiMap[$step['id']] ?? null;
                if (! $intervento) continue;

                $assegnati[] = (int) $step['id'];
                $tappe[] = [
                    'ordine'             => $ordine++,
                    'intervento'         => $intervento,
                    'ora_arrivo_stimata' => $this->secondsToTime((int) ($step['arrival'] ?? 0)),
                    'distanza'           => $step['distance'] ?? null,
                    'durata'             => $step['duration'] ?? null,
                    'fallback'           => false,
                ];
            }

            if (! empty($tappe)) {
                $proposte[] = [
                    'tecnico'         => $tecnico,
                    'veicolo'         => $veicolo,
                    'tappe'           => $tappe,
                    'distanza_totale' => $route['distance'] ?? null,
                    'durata_totale'   => $route['duration'] ?? null,
                ];
            }
        }

        // Secondo passaggio: assegna non assegnati ai tecnici livello 0 come fallback
        $nonAssegnati = array_values(array_filter($interventi, fn($i) => ! in_array((int) $i['id'], $assegnati)));
        $nonPianificabili = $this->assegnaFallback($nonAssegnati, $proposte, $tecniciMap, $competenze, $tipiMap, $veicoliNorm, $usatiCA);

        return [
            'non_geocodificati' => $nonGeo,
            'non_pianificabili' => $nonPianificabili,
            'proposte'          => $proposte,
        ];
    }

    private function assegnaFallback(array $nonAssegnati, array &$proposte, array $tecniciMap, array $competenze, array $tipiMap, array $veicoliNorm, int $usatiCA): array
    {
        if (empty($nonAssegnati)) return [];

        // Indice rapido: proposta esistente per tecnico_id
        $indiceProposte = [];
        foreach ($proposte as $idx => $p) {
            $indiceProposte[(int) $p['tecnico']['id']] = $idx;
        }

        $rimasti = [];

        foreach ($nonAssegnati as $intervento) {
            $codice = $intervento['tipo_intervento'];
            $tipoId = $tipiMap[$codice]['id'] ?? null;

            // Trova tecnici con livello 0 per questo tipo
            $candidati = [];
            foreach ($tecniciMap as $tecnicoId => $tecnico) {
                $livello = $competenze[$tecnicoId][$tipoId] ?? null;
                if ($livello !== 0) continue;

                // Numero tappe correnti (meno tappe = più capacità residua)
                $nTappe = isset($indiceProposte[$tecnicoId])
                    ? count($proposte[$indiceProposte[$tecnicoId]]['tappe'])
                    : 0;
                $candidati[$tecnicoId] = $nTappe;
            }

            if (empty($candidati)) {
                $rimasti[] = $intervento;
                continue;
            }

            asort($candidati);
            $bestId = array_key_first($candidati);

            $tappa = [
                'ordine'             => 0, // sarà ricalcolato sotto
                'intervento'         => $intervento,
                'ora_arrivo_stimata' => null,
                'distanza'           => null,
                'durata'             => null,
                'fallback'           => true,
            ];

            if (isset($indiceProposte[$bestId])) {
                $idx    = $indiceProposte[$bestId];
                $ordine = count($proposte[$idx]['tappe']) + 1;
                $tappa['ordine'] = $ordine;
                $proposte[$idx]['tappe'][] = $tappa;
            } else {
                // Tecnico senza trip ORS: crea nuova proposta
                $tecnico = $tecniciMap[$bestId];
                $veicolo = $veicoliNorm[count($proposte) - $usatiCA] ?? null;
                $tappa['ordine'] = 1;
                $nuovaIdx = count($proposte);
                $proposte[] = [
                    'tecnico'         => $tecnico,
                    'veicolo'         => $veicolo,
                    'tappe'           => [$tappa],
                    'distanza_totale' => null,
                    'durata_totale'   => null,
                ];
                $indiceProposte[$bestId] = $nuovaIdx;
            }
        }

        return $rimasti;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function nomeCliente(array $i): string
    {
        return $i['cliente_tipo'] === 'persona_fisica'
            ? trim(($i['cliente_cognome'] ?? '') . ' ' . ($i['cliente_nome'] ?? ''))
            : ($i['ragsoc'] ?? '—');
    }

    private function timeToSeconds(string $time): int
    {
        [$h, $m] = explode(':', $time);
        return (int) $h * 3600 + (int) $m * 60;
    }

    private function secondsToTime(int $s): string
    {
        return sprintf('%02d:%02d:00', floor($s / 3600), ($s / 60) % 60);
    }
}
