<?php

namespace App\Controllers;

use App\Models\InterventoModel;
use App\Models\UserModel;
use App\Models\ViaggioModel;
use App\Models\ViaggioTappaModel;
use App\Models\TecnicoCompetenzaModel;
use App\Models\TipoInterventoModel;

class Calendario extends BaseController
{
    public function index(): string
    {
        $tecnici = (new UserModel())
            ->select('id, nome, cognome, colore')
            ->whereAssegnabile()
            ->where('active', 1)
            ->orderBy('cognome, nome')
            ->asArray()
            ->findAll();

        $competenzaModel      = new TecnicoCompetenzaModel();
        $competenzePerTecnico = [];
        foreach ($tecnici as $t) {
            $rows = $competenzaModel->perTecnico($t['id']);
            $map  = [];
            foreach ($rows as $r) {
                $map[(int) $r['tipo_intervento_id']] = (int) $r['livello'];
            }
            $competenzePerTecnico[$t['id']] = $map;
        }

        $tipiPerCodice = [];
        foreach (TipoInterventoModel::comeListaCompleta() as $tipo) {
            $tipiPerCodice[$tipo['codice']] = [
                'id'             => (int) $tipo['id'],
                'nome'           => $tipo['nome'],
                'icona'          => $tipo['icona'] ?? 'fa-wrench',
                'durata_default' => (int) ($tipo['durata_default'] ?? 60),
            ];
        }

        $pool = (new InterventoModel())
            ->select('interventi.id, interventi.tipo_intervento, interventi.descrizione,
                      interventi.luogo_intervento, interventi.priorita, interventi.cliente_id,
                      interventi.data_entro,
                      c.ragsoc, c.cognome AS cliente_cognome, c.nome AS cliente_nome,
                      c.tipo AS cliente_tipo, c.citta AS cliente_citta,
                      c.lat, c.lng')
            ->join('clienti c', 'c.id = interventi.cliente_id AND c.deleted_at IS NULL', 'left')
            ->where('interventi.stato', 'da_pianificare')
            ->findAll();

        /** 
         * UTILIZZO FORMULA DI HAVERSINE 
         * per calcolo distante empiriche matematiche 
         **/
        // Coordinate della sede — servono come punto di partenza per la distanza
        $sedeLat = (float) (setting('Azienda.sede_lat') ?? 0);
        $sedeLng = (float) (setting('Azienda.sede_lng') ?? 0);
        
        // Calcolo distanza per ogni intervento e ordinamento: priorità prima, distanza dopo
        if ($sedeLat && $sedeLng) {
            foreach ($pool as &$i) {
                $i['distanza_km'] = ($i['lat'] && $i['lng'])
                    ? self::haversineKm($sedeLat, $sedeLng, (float)$i['lat'], (float)$i['lng'])
                    : null;
            }
            unset($i);
        
            $ordPriorita = ['urgente' => 0, 'ordinario' => 1, 'programmato' => 2];
            usort($pool, function ($a, $b) use ($ordPriorita) {
                $pa = $ordPriorita[$a['priorita']] ?? 3;
                $pb = $ordPriorita[$b['priorita']] ?? 3;
                if ($pa !== $pb) return $pa - $pb;
                // a parità di priorità: più vicino prima, non geocodificati in fondo
                $da = $a['distanza_km'] ?? PHP_FLOAT_MAX;
                $db = $b['distanza_km'] ?? PHP_FLOAT_MAX;
                return $da <=> $db;
            });
        }

        $poolPerTipo = [];
        foreach ($pool as $i) {
            $poolPerTipo[$i['tipo_intervento']][] = $i;
        }

        // Tutti gli interventi aperti con una scadenza impostata, ordinati per urgenza
        $scadenzeSettimana = (new InterventoModel())
            ->select('interventi.id, interventi.data_entro,
                      c.ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome')
            ->join('clienti c', 'c.id = interventi.cliente_id', 'left')
            ->where('interventi.data_entro IS NOT NULL', null, false)
            ->whereNotIn('interventi.stato', ['completato', 'annullato'])
            ->orderBy('interventi.data_entro', 'ASC')
            ->findAll();

        return view('calendario/index', [
            'title'                => 'Calendario',
            'page_title'           => 'Calendario Interventi',
            'tecnici'              => $tecnici,
            'competenzePerTecnico' => $competenzePerTecnico,
            'tipiPerCodice'        => $tipiPerCodice,
            'pool'                 => $pool,
            'poolPerTipo'          => $poolPerTipo,
            'oraInizio'            => setting('Tecnici.orario_inizio') ?? '08:00',
            'scadenzeSettimana'    => $scadenzeSettimana,
        ]);
    }

    public function eventi()
    {
        $model     = new InterventoModel();
        $start     = $this->request->getGet('start')      ?? date('Y-m-01');
        $end       = $this->request->getGet('end')        ?? date('Y-m-t');
        $tecnicoId = (int) ($this->request->getGet('tecnico_id') ?? 0);

        $q = $model
            ->select('interventi.id, interventi.tipo_intervento, interventi.stato,
                      interventi.data_pianificata, interventi.durata_effettiva, interventi.descrizione,
                      interventi.data_entro,
                      c.ragsoc AS cliente_ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome,
                      c.citta AS cliente_citta,
                      u.nome AS tecnico_nome, u.cognome AS tecnico_cognome, u.colore AS tecnico_colore,
                      ti.durata_default AS tipo_durata, ti.nome AS tipo_nome, ti.icona AS tipo_icona')
            ->join('clienti c',          'c.id = interventi.cliente_id',          'left')
            ->join('users u',            'u.id = interventi.tecnico_id',           'left')
            ->join('tipi_intervento ti', 'ti.codice = interventi.tipo_intervento', 'left')
            ->where('interventi.data_pianificata >=', $start)
            ->where('interventi.data_pianificata <',  $end)
            ->where('interventi.stato !=', 'annullato')
            ->where('interventi.data_pianificata IS NOT NULL');

        if ($tecnicoId) {
            $q->where('interventi.tecnico_id', $tecnicoId);
        }

        $interventi = $q->findAll();

        $events = [];
        foreach ($interventi as $i) {
            $cliente = $i['cliente_ragsoc']
                ?: trim(($i['cliente_nome'] ?? '') . ' ' . ($i['cliente_cognome'] ?? ''))
                ?: '—';
            $durata  = max(60, (int) ($i['durata_effettiva'] ?: $i['tipo_durata'] ?: 60));
            $colore  = $i['tecnico_colore'] ?: '#6c757d';
            $tecnico = $i['tecnico_nome']
                ? trim($i['tecnico_nome'] . ' ' . $i['tecnico_cognome'])
                : 'Non assegnato';

            $endDt = date('Y-m-d H:i:s', strtotime($i['data_pianificata']) + $durata * 60);

            $events[] = [
                'id'    => $i['id'],
                'title' => $cliente,
                'start' => $i['data_pianificata'],
                'end'   => $endDt,
                'color' => $colore,
                'url'   => base_url('interventi/' . $i['id'] . '?from=calendario'),
                'extendedProps' => [
                    'tecnico'    => $tecnico,
                    'tecnico_id' => $i['tecnico_id'] ?? null,
                    'tipo'       => $i['tipo_nome'] ?: $i['tipo_intervento'],
                    'icona'      => $i['tipo_icona'] ?: 'fa-tools',
                    'stato'      => $i['stato'],
                    'descrizione'=> $i['descrizione'] ?: '',
                    'citta'      => $i['cliente_citta'] ?: '',
                    'data_entro' => $i['data_entro'] ?? null,
                ],
            ];
        }

        return $this->response->setJSON($events);
    }

    public function generaViaggioGiornata()
    {
        $data      = $this->request->getPost('data');
        $tecnicoId = (int) ($this->request->getPost('tecnico_id') ?? 0);

        if (! $data || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            return redirect()->to('calendario')->with('error', 'Data non valida.');
        }

        $interventi = (new InterventoModel())
            ->select('interventi.id, interventi.tecnico_id, interventi.data_pianificata, interventi.ora_inizio')
            ->where('DATE(interventi.data_pianificata)', $data)
            ->whereIn('interventi.stato', ['pianificato', 'in_corso'])
            ->where('interventi.tecnico_id IS NOT NULL', null, false)
            ->orderBy('interventi.data_pianificata', 'ASC')
            ->findAll();

        if (empty($interventi)) {
            return redirect()->to('calendario')->with('error', 'Nessun intervento pianificato per questa data.');
        }

        // raggruppa per tecnico
        $perTecnico = [];
        foreach ($interventi as $inv) {
            if ($tecnicoId && $inv['tecnico_id'] !== (string) $tecnicoId) continue;
            $perTecnico[$inv['tecnico_id']][] = $inv;
        }

        if (empty($perTecnico)) {
            return redirect()->to('calendario')->with('error', 'Nessun intervento trovato per il tecnico selezionato per la data del ' . date('d/m/Y', strtotime($data)) . '.');
        }

        $viaggioModel = new ViaggioModel();
        $tappaModel   = new ViaggioTappaModel();
        $db           = db_connect();
        $creati       = 0;

        // ID interventi già presenti in qualche tappa
        $idGiaAssegnati = array_column(
            $db->table('viaggi_tappe')->select('intervento_id')->get()->getResultArray(),
            'intervento_id'
        );

        // tecnici che hanno già un viaggio in questa data (caricato una volta sola)
        $tecnicoConViaggio = array_column(
            $db->table('viaggi')->select('tecnico_id')->where('data', $data)->get()->getResultArray(),
            'tecnico_id'
        );

        foreach ($perTecnico as $tId => $interventiTecnico) {
            if (in_array($tId, $tecnicoConViaggio)) continue;

            // filtra interventi già in un'altra tappa
            $daInserire = array_filter(
                $interventiTecnico,
                fn($inv) => ! in_array($inv['id'], $idGiaAssegnati)
            );

            if (empty($daInserire)) continue;

            $viaggioId = $viaggioModel->insert([
                'tecnico_id' => $tId,
                'data'       => $data,
                'stato'      => 'autorizzato',
            ]);

            foreach (array_values($daInserire) as $ordine => $inv) {
                $tappaModel->insert([
                    'viaggio_id'    => $viaggioId,
                    'intervento_id' => $inv['id'],
                    'ordine'        => $ordine + 1,
                ]);
            }

            $creati++;
        }

        if ($creati === 0) {
            return redirect()->to('viaggi/pdf/' . $data)
                ->with('info', 'I viaggi per questa data esistevano già.');
        }

        return redirect()->to('viaggi/pdf/' . $data);
    }

    public function sposta()
    {
        $id    = (int) $this->request->getPost('id');
        $start = $this->request->getPost('start');

        if (! $id || ! $start) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msg' => 'Dati mancanti']);
        }

        $data = date('Y-m-d H:i:s', strtotime($start));

        (new InterventoModel())->update($id, ['data_pianificata' => $data]);

        return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
    }

    // Distanza in km tra due coordinate geografiche (formula di Haversine)
    private static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

}
