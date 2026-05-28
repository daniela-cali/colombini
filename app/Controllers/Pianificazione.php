<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\InterventoModel;
use App\Models\TecnicoCompetenzaModel;
use App\Models\TipoInterventoModel;
use App\Models\ViaggioModel;
use App\Models\ViaggioTappaModel;
use CodeIgniter\HTTP\ResponseInterface;

class Pianificazione extends BaseController
{
    public function index(): string
    {
        $data     = $this->request->getGet('data') ?? date('Y-m-d');
        $lunedi   = date('Y-m-d', strtotime('monday this week', strtotime($data)));
        $domenica = date('Y-m-d', strtotime($lunedi . ' +6 days'));

        $tecnici = (new UserModel())
            ->whereAssegnabile()
            ->orderBy('cognome')
            ->findAll();

        $competenzaModel      = new TecnicoCompetenzaModel();
        $competenzePerTecnico = [];
        foreach ($tecnici as $t) {
            $rows = $competenzaModel->perTecnico($t->id);
            $map  = [];
            foreach ($rows as $r) {
                $map[(int) $r['tipo_intervento_id']] = (int) $r['livello'];
            }
            $competenzePerTecnico[$t->id] = $map;
        }

        $tipiCompleti  = TipoInterventoModel::comeListaCompleta();
        $tipiPerCodice = [];
        foreach ($tipiCompleti as $tipo) {
            $tipiPerCodice[$tipo['codice']] = [
                'id'             => (int) $tipo['id'],
                'nome'           => $tipo['nome'],
                'icona'          => $tipo['icona'] ?? 'fa-wrench',
                'durata_default' => (int) ($tipo['durata_default'] ?? 60),
            ];
        }

        // Pool: tutti gli interventi da_pianificare
        $interventi = $this->queryInterventi()
            ->where('interventi.stato', 'da_pianificare')
            ->orderBy('FIELD(interventi.priorita, "urgente", "ordinario", "programmato")')
            ->findAll();

        $poolPerTipo = [];
        foreach ($interventi as $inv) {
            $poolPerTipo[$inv['tipo_intervento']][] = $inv;
        }

        // Interventi pianificati/in_corso per tutta la settimana
        $giornataInterventi = $this->queryInterventi()
            ->select('interventi.tecnico_id, interventi.stato,
                      u_tec.nome AS tecnico_nome, u_tec.cognome AS tecnico_cognome, u_tec.colore AS tecnico_colore')
            ->join('users u_tec', 'u_tec.id = interventi.tecnico_id', 'left')
            ->whereIn('interventi.stato', ['pianificato', 'in_corso'])
            ->where('DATE(interventi.data_pianificata) >=', $lunedi)
            ->where('DATE(interventi.data_pianificata) <=', $domenica)
            ->orderBy('interventi.data_pianificata', 'ASC')
            ->findAll();

        // Raggruppa per giorno
        $giornataPerGiorno = [];
        foreach ($giornataInterventi as $inv) {
            $giorno = date('Y-m-d', strtotime($inv['data_pianificata']));
            $giornataPerGiorno[$giorno][] = $inv;
        }

        // Settimana con conteggi (senza query aggiuntive)
        $settimana = [];
        for ($i = 0; $i < 7; $i++) {
            $giorno      = date('Y-m-d', strtotime($lunedi . " +{$i} days"));
            $settimana[] = [
                'data'  => $giorno,
                'count' => count($giornataPerGiorno[$giorno] ?? []),
            ];
        }

        return view('pianificazione/index', [
            'title'                => 'Pianificazione',
            'page_title'           => 'Pianificazione Interventi',
            'data'                 => $data,
            'lunedi'               => $lunedi,
            'settimana'            => $settimana,
            'tecnici'              => $tecnici,
            'competenzePerTecnico' => $competenzePerTecnico,
            'tipiPerCodice'        => $tipiPerCodice,
            'interventi'           => $interventi,
            'poolPerTipo'          => $poolPerTipo,
            'giornataPerGiorno'    => $giornataPerGiorno,
            'priorita'             => InterventoModel::PRIORITA,
            'oraInizio'            => setting('Tecnici.orario_inizio') ?? '08:30',
        ]);
    }

    public function salva(): ResponseInterface
    {
        $json         = $this->request->getJSON(true);
        $data         = $json['data']         ?? date('Y-m-d');
        $assegnazioni = $json['assegnazioni'] ?? [];

        if (empty($assegnazioni)) {
            return $this->response->setJSON(['errore' => 'Nessuna assegnazione da salvare.']);
        }

        $tecnicoIds   = array_map(fn($a) => (int) $a['tecnico_id'], $assegnazioni);
        $viaggioModel = new ViaggioModel();
        $tappaModel   = new ViaggioTappaModel();

        // Blocca se uno o più tecnici hanno già un viaggio approvato in quella data
        $approvati = $viaggioModel
            ->select('viaggi.tecnico_id, u.cognome, u.nome')
            ->join('users u', 'u.id = viaggi.tecnico_id')
            ->where('viaggi.data', $data)
            ->whereIn('viaggi.stato', ['autorizzato', 'in_corso', 'completato'])
            ->whereIn('viaggi.tecnico_id', $tecnicoIds)
            ->findAll();

        if (!empty($approvati)) {
            $nomi = array_map(fn($r) => $r['cognome'] . ' ' . $r['nome'], $approvati);
            return $this->response->setJSON([
                'ok'     => false,
                'errore' => 'Viaggio già approvato per: ' . implode(', ', $nomi) . '. Riapri prima il viaggio per poterlo modificare.',
            ]);
        }

        // Elimina le bozze esistenti per i tecnici coinvolti in questa data

        $oldBozze = $viaggioModel
            ->where('data', $data)
            ->where('stato', 'bozza')
            ->whereIn('tecnico_id', $tecnicoIds)
            ->findAll();

        // Salvo i veicoli già assegnati alle bozze precedenti per non perderli
        $vecchiVeicoli = [];
        foreach ($oldBozze as $v) {
            if ($v['veicolo_id']) {
                $vecchiVeicoli[(int) $v['tecnico_id']] = (int) $v['veicolo_id'];
            }
            db_connect()->table('viaggi_tappe')->where('viaggio_id', $v['id'])->delete();
            db_connect()->table('viaggi')->where('id', $v['id'])->delete();
        }

        // Crea i nuovi viaggi bozza
        $creati = 0;
        foreach ($assegnazioni as $a) {
            $tappe = $a['tappe'] ?? [];
            if (empty($tappe)) continue;

            $tecnicoId = (int) $a['tecnico_id'];
            $viaggioId = $viaggioModel->insert([
                'tecnico_id' => $tecnicoId,
                'veicolo_id' => $vecchiVeicoli[$tecnicoId] ?? null,
                'data'       => $data,
                'stato'      => 'bozza',
            ]);

            $batch = [];
            foreach ($tappe as $idx => $tappa) {
                $batch[] = [
                    'viaggio_id'              => $viaggioId,
                    'intervento_id'           => (int) $tappa['intervento_id'],
                    'ordine'                  => $idx + 1,
                    'posizionato_manualmente' => 1,
                ];
            }
            $tappaModel->insertBatch($batch);
            $creati++;
        }

        return $this->response->setJSON([
            'ok'     => true,
            'creati' => $creati,
            'msg'    => $creati . ($creati === 1 ? ' viaggio creato' : ' viaggi creati') . ' in bozza.',
        ]);
    }

    private function queryInterventi(): InterventoModel
    {
        return (new InterventoModel())
            ->select('interventi.id, interventi.tipo_intervento, interventi.descrizione,
                      interventi.luogo_intervento, interventi.citta, interventi.priorita,
                      interventi.data_pianificata, interventi.cliente_id,
                      c.ragsoc, c.cognome AS cliente_cognome, c.nome AS cliente_nome, c.tipo AS cliente_tipo')
            ->join('clienti c', 'c.id = interventi.cliente_id AND c.deleted_at IS NULL', 'left');
    }
}
