<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\InterventoModel;
use App\Models\RichiestaModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $clientiModel    = new ClienteModel();
        $interventiModel = new InterventoModel();
        $richiesteModel  = new RichiestaModel();
        $usersModel      = new UserModel();

        $interventiAperti = $interventiModel
            ->whereIn('stato', ['pianificato', 'in_corso'])
            ->countAllResults();

        $interventiMese = $interventiModel
            ->where('stato', 'completato')
            ->where('MONTH(data_completamento)', date('n'))
            ->where('YEAR(data_completamento)', date('Y'))
            ->countAllResults();

        return view('dashboard/index', [
            'title'      => 'Dashboard',
            'page_title' => 'Dashboard',
            'stats' => [
                'clienti'           => $clientiModel->where('stato', 1)->countAllResults(),
                'impianti'          => 0,
                'interventi_aperti' => $interventiAperti,
                'interventi_mese'   => $interventiMese,
            ],
            'ultime_richieste'  => $richiesteModel->ultime(6),
            'ultimi_interventi' => $interventiModel->conDettagli(6),
            'tecnici'           => $usersModel->where('ruolo', 'tecnico')->orderBy('cognome')->findAll(),
            'tipi'              => InterventoModel::TIPI,
            'stati_intervento'  => InterventoModel::STATI,
            'stati_richiesta'   => RichiestaModel::STATI,
        ]);
    }
}