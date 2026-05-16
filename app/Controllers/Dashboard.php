<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\InterventoModel;
use App\Models\TipoInterventoModel;
use App\Models\RichiestaModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $utente = auth()->user();

        if ($utente->ruolo === 'tecnico') {
            return $this->dashboardTecnico($utente);
        }

        return $this->dashboardAdmin();
    }

    private function dashboardAdmin(): string
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
            'riepilogo_tecnici' => $interventiModel->riepilogoPerTecnico(),
            'tipi'              => TipoInterventoModel::comeLista(),
            'icone'             => array_column(TipoInterventoModel::comeListaCompleta(), 'icona', 'codice'),
            'stati_intervento'  => InterventoModel::STATI,
            'stati_richiesta'   => RichiestaModel::STATI,
        ]);
    }

    private function dashboardTecnico(\CodeIgniter\Shield\Entities\User $utente): string
    {
        $interventiModel = new InterventoModel();
        $id = $utente->id;

        $interventi = $interventiModel->perTecnico($id);

        $mese = date('Y-m');
        $cPianificati      = 0;
        $cInCorso          = 0;
        $cCompletatiMese   = 0;
        $cTotaleCompletati = 0;

        foreach ($interventi as $inv) {
            if ($inv['stato'] === 'pianificato') $cPianificati++;
            if ($inv['stato'] === 'in_corso')    $cInCorso++;
            if ($inv['stato'] === 'completato') {
                $cTotaleCompletati++;
                if (substr($inv['data_completamento'] ?? '', 0, 7) === $mese) $cCompletatiMese++;
            }
        }

        $prossimi = array_filter($interventi, fn($i) =>
            in_array($i['stato'], ['pianificato', 'in_corso'])
        );
        usort($prossimi, fn($a, $b) =>
            strcmp($a['data_pianificata'] ?? '', $b['data_pianificata'] ?? '')
        );

        return view('dashboard/tecnico', [
            'title'            => 'La mia agenda',
            'page_title'       => 'La mia agenda',
            'tecnico'          => $utente,
            'interventi'       => $interventi,
            'prossimi'         => array_slice($prossimi, 0, 10),
            'stats' => [
                'pianificati'       => $cPianificati,
                'in_corso'          => $cInCorso,
                'completati_mese'   => $cCompletatiMese,
                'totale_completati' => $cTotaleCompletati,
            ],
            'tipi'             => TipoInterventoModel::comeLista(),
            'icone'            => array_column(TipoInterventoModel::comeListaCompleta(), 'icona', 'codice'),
            'stati'            => InterventoModel::STATI,
        ]);
    }
}