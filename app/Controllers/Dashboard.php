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
        $interventiModel = new InterventoModel();

        $daPianificare = $interventiModel->conDettagli(0, null, 'da_pianificare');
        $quaderni = [];
        foreach ($daPianificare as $inv) {
            $quaderni[$inv['tipo_intervento']][] = $inv;
        }

        $mese = date('n');
        $anno = date('Y');

        $richiesteModel   = new RichiestaModel();
        $nuoveRichieste   = $richiesteModel->where('stato', 'nuova')->countAllResults();
        $ultime_richieste = $richiesteModel->ultime(5);

        return view('dashboard/index', [
            'title'      => 'Dashboard',
            'page_title' => 'Dashboard',
            'stats' => [
                'da_pianificare'  => $interventiModel->where('stato', 'da_pianificare')->countAllResults(),
                'pianificati'     => $interventiModel->where('stato', 'pianificato')->countAllResults(),
                'in_corso'        => $interventiModel->where('stato', 'in_corso')->countAllResults(),
                'completati_mese' => $interventiModel->where('stato', 'completato')
                    ->where('MONTH(data_completamento)', $mese)
                    ->where('YEAR(data_completamento)', $anno)
                    ->countAllResults(),
            ],
            'quaderni'         => $quaderni,
            'tipi'             => TipoInterventoModel::comeLista(),
            'icone'            => array_column(TipoInterventoModel::comeListaCompleta(), 'icona', 'codice'),
            'stati'            => InterventoModel::STATI,
            'tecnici'          => (new UserModel())->whereAssegnabile()->orderBy('cognome')->findAll(),
            'nuove_richieste'  => $nuoveRichieste,
            'ultime_richieste' => $ultime_richieste,
            'stati_richiesta'  => RichiestaModel::STATI,
        ]);
    }

    private function dashboardTecnico(\CodeIgniter\Shield\Entities\User $utente): string
    {
        $interventiModel = new InterventoModel();
        $clientiModel    = new ClienteModel();
        $id = $utente->id;

        $interventiMiei = $interventiModel->conDettagli(0, $id);

        $mese = date('Y-m');
        $cPianificati      = 0;
        $cInCorso          = 0;
        $cCompletatiMese   = 0;
        $cTotaleCompletati = 0;

        foreach ($interventiMiei as $inv) {
            if ($inv['stato'] === 'pianificato') $cPianificati++;
            if ($inv['stato'] === 'in_corso')    $cInCorso++;
            if ($inv['stato'] === 'completato') {
                $cTotaleCompletati++;
                if (substr($inv['data_completamento'] ?? '', 0, 7) === $mese) $cCompletatiMese++;
            }
        }

        $prossimi = array_filter($interventiMiei, fn($i) =>
            in_array($i['stato'], ['da_pianificare', 'pianificato', 'in_corso'])
        );
        usort($prossimi, fn($a, $b) =>
            strcmp($a['data_pianificata'] ?? '', $b['data_pianificata'] ?? '')
        );

        $prossimiPerGiorno = [];
        foreach (array_slice($prossimi, 0, 30) as $inv) {
            $giorno = $inv['data_pianificata']
                ? date('Y-m-d', strtotime($inv['data_pianificata']))
                : 'senza_data';
            $prossimiPerGiorno[$giorno][] = $inv;
        }

        return view('dashboard/tecnico', [
            'title'            => 'La mia agenda',
            'page_title'       => 'La mia agenda',
            'tecnico'          => $utente,
            'prossimiPerGiorno'=> $prossimiPerGiorno,
            'stats' => [
                'pianificati'       => $cPianificati,
                'in_corso'          => $cInCorso,
                'completati_mese'   => $cCompletatiMese,
                'totale_completati' => $cTotaleCompletati,
            ],
            'tipi'             => TipoInterventoModel::comeLista(),
            'icone'            => array_column(TipoInterventoModel::comeListaCompleta(), 'icona', 'codice'),
            'stati'            => InterventoModel::STATI,
            'clienti'          => $clientiModel->where('stato', 1)->orderBy('ragsoc')->orderBy('cognome')->findAll(),
        ]);
    }
}