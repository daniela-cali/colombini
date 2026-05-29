<?php

namespace App\Controllers;

use App\Models\ViaggioModel;
use App\Models\ViaggioTappaModel;
use App\Models\VeicoloModel;
use App\Models\InterventoModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Viaggi extends BaseController
{
    public function index(): string
    {
        $settimana   = $this->request->getGet('settimana') ?? date('Y-m-d');
        $lunedi = date('Y-m-d', strtotime('monday this week', strtotime($settimana)));
        $domenica = date('Y-m-d', strtotime('+6 days', strtotime($lunedi)));
        /*Per i link prev/next*/
        $settPrecedente = date('Y-m-d', strtotime('-7 days', strtotime($lunedi)));
        $settSuccessiva = date('Y-m-d', strtotime('+7 days', strtotime($lunedi)));


        $model  = new ViaggioModel();
        $utente = auth()->user();

        $tecnicoFiltro = ($utente->ruolo === 'tecnico') ? $utente->id : null;

        return view('viaggi/index', [
            'title'      => 'Viaggi',
            'page_title' => 'Viaggi',
            'lunedi'          => $lunedi,
            'domenica'        => $domenica,
            'settPrecedente'  => $settPrecedente,
            'settSuccessiva'  => $settSuccessiva,            
            'viaggi'     => $model->perRange($lunedi, $domenica, $tecnicoFiltro),
            'stati'      => ViaggioModel::STATI,
        ]);
    }

    public function show(int $id)
    {
        $model   = new ViaggioModel();
        $viaggio = $model->conTappe($id);

        if (! $viaggio) {
            return redirect()->to('viaggi')->with('error', 'Viaggio non trovato.');
        }

        $veicoli = (new VeicoloModel())->comeLista();

        // Veicoli riservati: appartengono a un tecnico con richiede_cambio_auto = 1
        // che ha un viaggio con almeno una tappa nella stessa data (esclude questo viaggio)
        $riservatiRows = db_connect()
            ->table('users u')
            ->select("u.veicolo_id, CONCAT(u.cognome, ' ', u.nome) AS tecnico_nome")
            ->join('viaggi v',        'v.tecnico_id = u.id',    'inner')
            ->join('viaggi_tappe vt', 'vt.viaggio_id = v.id',  'inner')
            ->where('u.richiede_cambio_auto', 1)
            ->where('u.veicolo_id IS NOT NULL')
            ->where('v.data', $viaggio['data'])
            ->where('v.id !=', $id)
            ->groupBy('u.id, u.veicolo_id')
            ->get()->getResultArray();

        $veicoli_riservati = [];
        foreach ($riservatiRows as $r) {
            $veicoli_riservati[(int) $r['veicolo_id']] = $r['tecnico_nome'];
        }

        // Veicoli già assegnati ad altri viaggi nella stessa data
        $impegnatiRows = db_connect()
            ->table('viaggi')
            ->select('veicolo_id')
            ->where('data', $viaggio['data'])
            ->where('id !=', $id)
            ->where('veicolo_id IS NOT NULL')
            ->get()->getResultArray();

        $veicoli_impegnati = array_column($impegnatiRows, 'veicolo_id');

        return view('viaggi/show', [
            'title'             => 'Viaggio del ' . date('d/m/Y', strtotime($viaggio['data'])),
            'page_title'        => 'Dettaglio Viaggio',
            'viaggio'           => $viaggio,
            'stati'             => ViaggioModel::STATI,
            'stati_int'         => InterventoModel::STATI,
            'priorita'          => InterventoModel::PRIORITA,
            'veicoli'            => $veicoli,
            'veicoli_riservati'  => $veicoli_riservati,
            'veicoli_impegnati'  => $veicoli_impegnati,
        ]);
    }

    public function assegnaVeicolo(int $id)
    {
        $model   = new ViaggioModel();
        $viaggio = $model->find($id);

        if (! $viaggio || ! in_array($viaggio['stato'], ['bozza', 'autorizzato'])) {
            return redirect()->to('viaggi/' . $id)->with('error', 'Operazione non consentita.');
        }

        $veicoloId = $this->request->getPost('veicolo_id') ?: null;

        if (! $veicoloId) {
            return redirect()->to('viaggi/' . $id)->with('error', 'Seleziona un veicolo prima di salvare.');
        }

        $model->update($id, ['veicolo_id' => $veicoloId]);

        return redirect()->to('viaggi/' . $id)->with('success', 'Veicolo aggiornato.');
    }

    public function autorizza(int $id)
    {
        $model   = new ViaggioModel();
        $viaggio = $model->find($id);

        if (! $viaggio || $viaggio['stato'] !== 'bozza') {
            return redirect()->to('viaggi/' . $id)->with('error', 'Operazione non consentita.');
        }

        if (empty($viaggio['veicolo_id'])) {
            return redirect()->to('viaggi/' . $id)->with('error', 'Impossibile approvare: nessun mezzo assegnato al viaggio.');
        }

        $tappeModel    = new ViaggioTappaModel();
        $interventoModel = new InterventoModel();
        $tappe         = $tappeModel->where('viaggio_id', $id)->findAll();

        foreach ($tappe as $tappa) {
            $interventoModel->update($tappa['intervento_id'], [
                'tecnico_id'      => $viaggio['tecnico_id'],
                'data_pianificata' => $viaggio['data'],
                'stato'           => 'pianificato',
            ]);
        }

        $model->update($id, ['stato' => 'autorizzato']);

        return redirect()->to('viaggi/' . $id)->with('success', 'Viaggio approvato.');
    }

    public function riapri(int $id)
    {
        $model   = new ViaggioModel();
        $viaggio = $model->find($id);

        if (! $viaggio) {
            return redirect()->to('viaggi')->with('error', "Viaggio #{$id} non trovato nel database.");
        }

        if ($viaggio['stato'] !== 'autorizzato') {
            return redirect()->to('viaggi/' . $id)->with('error', 'Il viaggio è in stato "' . $viaggio['stato'] . '" e non può essere riaperto.');
        }

        $model->update($id, ['stato' => 'bozza']);

        return redirect()->to('viaggi/' . $id)->with('success', 'Viaggio riaperto in bozza.');
    }

    public function annulla(int $id)
    {
        $model   = new ViaggioModel();
        $viaggio = $model->find($id);

        if (! $viaggio || ! in_array($viaggio['stato'], ['bozza', 'autorizzato'])) {
            return redirect()->to('viaggi/' . $id)->with('error', 'Operazione non consentita.');
        }

        $tappeModel      = new ViaggioTappaModel();
        $interventoModel = new InterventoModel();
        $tappe           = $tappeModel->where('viaggio_id', $id)->findAll();

        foreach ($tappe as $tappa) {
            $interventoModel->update($tappa['intervento_id'], [
                'tecnico_id'      => null,
                'data_pianificata' => null,
                'stato'           => 'da_pianificare',
            ]);
        }

        $tappeModel->where('viaggio_id', $id)->delete();
        $model->delete($id, true);

        return redirect()->to('viaggi')->with('success', 'Viaggio annullato e interventi rimessi in coda.');
    }

    public function pdfViaggio(int $id)
    {
        $viaggio = (new ViaggioModel())->conTappe($id);

        if (! $viaggio) {
            return redirect()->to('viaggi')->with('error', 'Viaggio non trovato.');
        }

        $logoPath   = setting('Azienda.sede_logo_path') ?? '';
        $logoBase64 = '';
        if ($logoPath && is_file(FCPATH . $logoPath)) {
            $mime       = mime_content_type(FCPATH . $logoPath) ?: 'image/png';
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents(FCPATH . $logoPath));
        }

        $azienda = [
            'logo'      => $logoBase64,
            'nome'      => setting('Azienda.sede_nome')      ?? 'Colombini SNC',
            'indirizzo' => setting('Azienda.sede_indirizzo') ?? '',
            'cap'       => setting('Azienda.sede_cap')       ?? '',
            'citta'     => setting('Azienda.sede_citta')     ?? '',
            'telefono'  => setting('Azienda.sede_telefono')  ?? '',
        ];

        $html = view('viaggi/pdf_viaggio', [
            'viaggio'  => $viaggio,
            'azienda'  => $azienda,
            'priorita' => InterventoModel::PRIORITA,
        ]);

        $opt = new Options();
        $opt->set('defaultFont', 'DejaVu Sans');
        $opt->set('isRemoteEnabled', false);
        $pdf = new Dompdf($opt);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $filename = 'viaggio-' . $viaggio['cognome'] . '-' . $viaggio['data'] . '.pdf';
        $download = $this->response->download($filename, $pdf->output(), true);
        $download->inline();

        return $download;
    }

    public function pdfGiornata(string $data)
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            return redirect()->to('viaggi')->with('error', 'Data non valida.');
        }

        $tappeModel = new ViaggioTappaModel();
        $viaggi     = (new ViaggioModel())
            ->select('viaggi.*, u.nome, u.cognome, u.colore, v.nome AS veicolo_nome, v.targa AS veicolo_targa')
            ->join('users u', 'u.id = viaggi.tecnico_id')
            ->join('veicoli v', 'v.id = viaggi.veicolo_id', 'left')
            ->where('viaggi.data', $data)
            ->whereIn('viaggi.stato', ['autorizzato', 'in_corso', 'completato'])
            ->orderBy('u.cognome')
            ->findAll();

        foreach ($viaggi as &$v) {
            $v['tappe'] = $tappeModel->perViaggio($v['id']);
        }
        unset($v);

        $logoPath   = setting('Azienda.sede_logo_path') ?? '';
        $logoBase64 = '';
        if ($logoPath && is_file(FCPATH . $logoPath)) {
            $mime       = mime_content_type(FCPATH . $logoPath) ?: 'image/png';
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents(FCPATH . $logoPath));
        }

        $azienda = [
            'logo'      => $logoBase64,
            'nome'      => setting('Azienda.sede_nome')      ?? 'Colombini SNC',
            'indirizzo' => setting('Azienda.sede_indirizzo') ?? '',
            'cap'       => setting('Azienda.sede_cap')       ?? '',
            'citta'     => setting('Azienda.sede_citta')     ?? '',
            'telefono'  => setting('Azienda.sede_telefono')  ?? '',
        ];

        $html = view('viaggi/pdf_giornata', [
            'data'     => $data,
            'viaggi'   => $viaggi,
            'azienda'  => $azienda,
            'priorita' => InterventoModel::PRIORITA,
        ]);

        $opt = new Options();
        $opt->set('defaultFont', 'DejaVu Sans');
        $opt->set('isRemoteEnabled', false);
        $pdf = new Dompdf($opt);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $filename = 'viaggi-' . $data . '.pdf';
        $download = $this->response->download($filename, $pdf->output(), true);
        $download->inline();

        return $download;
    }
}
