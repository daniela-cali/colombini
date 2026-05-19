<?php

namespace App\Controllers;

use App\Models\ViaggioModel;
use App\Models\ViaggioTappaModel;
use App\Services\AssegnazioneService;
use CodeIgniter\HTTP\ResponseInterface;

class Ottimizzazione extends BaseController
{
    public function index(): string
    {
        return view('ottimizzazione/index', [
            'title'      => 'Ottimizzazione Viaggi',
            'page_title' => 'Ottimizzazione Viaggi',
        ]);
    }

    public function genera(): ResponseInterface
    {
        $data = $this->request->getPost('data');

        if (! $data || ! strtotime($data)) {
            return $this->response->setJSON(['errore' => 'Data non valida.']);
        }

        $result = (new AssegnazioneService())->proponi($data);
        return $this->response->setJSON($result);
    }

    public function salva(): ResponseInterface
    {
        $json         = $this->request->getJSON(true);
        $data         = $json['data']         ?? null;
        $assegnazioni = $json['assegnazioni'] ?? [];

        if (! $data || empty($assegnazioni)) {
            return $this->response->setJSON(['errore' => 'Dati mancanti.']);
        }

        $viaggioModel = new ViaggioModel();
        $tappaModel   = new ViaggioTappaModel();
        $creati       = 0;

        foreach ($assegnazioni as $a) {
            $tappe = $a['tappe'] ?? [];
            if (empty($tappe)) continue;

            $viaggioId = $viaggioModel->insert([
                'tecnico_id'      => (int) $a['tecnico_id'],
                'veicolo_id'      => $a['veicolo_id'] ? (int) $a['veicolo_id'] : null,
                'data'            => $data,
                'stato'           => 'bozza',
                'distanza_totale' => $a['distanza_totale'] ?? null,
                'durata_totale'   => $a['durata_totale']   ?? null,
            ]);

            $batch = [];
            foreach ($tappe as $idx => $tappa) {
                $batch[] = [
                    'viaggio_id'              => $viaggioId,
                    'intervento_id'           => (int) $tappa['intervento_id'],
                    'ordine'                  => $idx + 1,
                    'posizionato_manualmente' => 1,
                    'distanza_da_precedente'  => $tappa['distanza'] ?? null,
                    'durata_da_precedente'    => $tappa['durata']   ?? null,
                    'ora_arrivo_stimata'      => $tappa['ora_arrivo'] ?? null,
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
}
