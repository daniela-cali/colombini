<?php

namespace App\Controllers;

use App\Models\InterventoModel;

class Calendario extends BaseController
{
    public function index(): string
    {
        return view('calendario/index', [
            'title'      => 'Calendario',
            'page_title' => 'Calendario Interventi',
        ]);
    }

    public function eventi()
    {
        $model = new InterventoModel();
        $start = $this->request->getGet('start') ?? date('Y-m-01');
        $end   = $this->request->getGet('end')   ?? date('Y-m-t');

        $interventi = $model
            ->select('interventi.id, interventi.tipo_intervento, interventi.stato,
                      interventi.data_pianificata, interventi.durata_effettiva, interventi.descrizione,
                      c.ragsoc AS cliente_ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome,
                      u.nome AS tecnico_nome, u.cognome AS tecnico_cognome, u.colore AS tecnico_colore,
                      ti.durata_default AS tipo_durata, ti.nome AS tipo_nome, ti.icona AS tipo_icona')
            ->join('clienti c',          'c.id = interventi.cliente_id',          'left')
            ->join('users u',            'u.id = interventi.tecnico_id',           'left')
            ->join('tipi_intervento ti', 'ti.codice = interventi.tipo_intervento', 'left')
            ->where('interventi.data_pianificata >=', $start)
            ->where('interventi.data_pianificata <',  $end)
            ->where('interventi.stato !=', 'annullato')
            ->where('interventi.data_pianificata IS NOT NULL')
            ->findAll();

        $events = [];
        foreach ($interventi as $i) {
            $cliente = $i['cliente_ragsoc']
                ?: trim(($i['cliente_nome'] ?? '') . ' ' . ($i['cliente_cognome'] ?? ''))
                ?: '—';
            $durata  = (int) ($i['durata_effettiva'] ?: $i['tipo_durata'] ?: 60);
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
                    'tecnico'     => $tecnico,
                    'tipo'        => $i['tipo_nome'] ?: $i['tipo_intervento'],
                    'icona'       => $i['tipo_icona'] ?: 'fa-tools',
                    'stato'       => $i['stato'],
                    'descrizione' => $i['descrizione'] ?: '',
                ],
            ];
        }

        return $this->response->setJSON($events);
    }
}
