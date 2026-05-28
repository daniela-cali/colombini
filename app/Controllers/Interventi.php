<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\InterventoModel;
use App\Models\TipoInterventoModel;
use App\Models\UserModel;
use App\Models\RichiestaModel;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class Interventi extends BaseController
{
    public function index(): string
    {
        $model      = new InterventoModel();
        /*Elvis operator: se vero, restituisce il valore stesso, altrimenti null */
        $tecnicoFiltro    = (int) $this->request->getGet('tecnico_id') ?: null;
        $statoFiltro      = (string) $this->request->getGet('stato') ?: null;
        $mostraCompletati = (bool) $this->request->getGet('mostra_completati');
        $interventi       = $model->conDettagli(0, $tecnicoFiltro, $statoFiltro, !$mostraCompletati && !$statoFiltro);
        $tipi       = TipoInterventoModel::comeLista();
        $icone      = array_column(TipoInterventoModel::comeListaCompleta(), 'icona', 'codice');

        // Raggruppa per tecnico, ordinando gli interventi per data pianificata
        $perTecnico   = [];
        $nonAssegnati = [];

        foreach ($interventi as $inv) {
            if ($inv['tecnico_id']) {
                $tid = $inv['tecnico_id'];
                if (! isset($perTecnico[$tid])) {
                    $perTecnico[$tid] = [
                        'info'       => [
                            'id'      => $tid,
                            'nome'    => $inv['tecnico_nome'],
                            'cognome' => $inv['tecnico_cognome'],
                            'colore'  => $inv['tecnico_colore'] ?? '#3b82f6',
                        ],
                        'interventi' => [],
                    ];
                }
                $perTecnico[$tid]['interventi'][] = $inv;
            } else {
                $nonAssegnati[] = $inv;
            }
        }

        // Ordina ogni gruppo per data_pianificata ASC (prima i più vicini)
        foreach ($perTecnico as &$gruppo) {
            usort($gruppo['interventi'], fn($a, $b) =>
                /* Se data_pianificata è null mette come fallback '9999-99-99' in modo da finire in fondo */
                strcmp($a['data_pianificata'] ?? '9999-99-99', $b['data_pianificata'] ?? '9999-99-99')
            );
        }
        unset($gruppo);

        // Ordina i tecnici per cognome
        uasort($perTecnico, fn($a, $b) =>
            strcmp($a['info']['cognome'] . $a['info']['nome'], $b['info']['cognome'] . $b['info']['nome'])
        );

        return view('interventi/index', [
            'title'            => 'Interventi',
            'page_title'       => 'Interventi',
            'perTecnico'       => $perTecnico,
            'nonAssegnati'     => $nonAssegnati,
            'tipi'             => $tipi,
            'icone'            => $icone,
            'stati'            => InterventoModel::STATI,
            'mostraCompletati' => $mostraCompletati,
            'statoFiltro'      => $statoFiltro,
        ]);
    }

    public function create(): string
    {
        $users     = new UserModel();
        $richieste = new RichiestaModel();
        $clienti   = new ClienteModel();

        $tecnico_id_pre  = (int) $this->request->getGet('tecnico_id');
        $richiesta_id_pre = (int) $this->request->getGet('richiesta_id');

        $richiesteList = $richieste
            ->select('richieste_assistenza.*, c.id AS cliente_id_resolved')
            ->join('clienti c', 'c.user_id = richieste_assistenza.user_id', 'left')
            ->where('richieste_assistenza.stato', 'nuova')
            ->orderBy('richieste_assistenza.created_at', 'DESC')
            ->findAll();

        return view('interventi/create', [
            'title'            => 'Nuovo Intervento',
            'page_title'       => 'Nuovo Intervento',
            'tecnici'          => $users->whereAssegnabile()->orderBy('cognome')->findAll(),
            'clienti'          => $clienti->where('stato', 1)->orderBy('ragsoc, cognome')->findAll(),
            'richieste'        => $richiesteList,
            'tipi'             => TipoInterventoModel::comeLista(),
            'tecnico_id_pre'   => $tecnico_id_pre,
            'richiesta_id_pre' => $richiesta_id_pre,
            'stati'            => InterventoModel::STATI,
            'priorita'         => InterventoModel::PRIORITA,
        ]);
    }

    public function store()
    {
        $rules = [
            'tipo_intervento' => 'required|in_list[' . implode(',', array_keys(TipoInterventoModel::comeLista())) . ']',
            'cliente_id'      => 'permit_empty|is_natural_no_zero',
            'tecnico_id'      => 'permit_empty|is_natural_no_zero',
            'data_pianificata'=> 'permit_empty|valid_date[Y-m-d\TH:i]',
            'descrizione'     => 'permit_empty|max_length[3000]',
            'note_interne'    => 'permit_empty|max_length[3000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataPianificata = $this->request->getPost('data_pianificata');
        if ($dataPianificata) {
            $dataPianificata = date('Y-m-d H:i:s', strtotime($dataPianificata));
        }

        $richiestaId = $this->request->getPost('richiesta_id') ?: null;

        $lat = $this->request->getPost('lat');
        $lng = $this->request->getPost('lng');

        $model = new InterventoModel();
        $id    = $model->insert([
            'richiesta_id'     => $richiestaId,
            'tipo_intervento'  => $this->request->getPost('tipo_intervento'),
            'luogo_intervento' => $this->request->getPost('luogo_intervento') ?: null,
            'citta'            => $this->request->getPost('citta') ?: null,
            'lat'              => $lat !== '' ? $lat : null,
            'lng'              => $lng !== '' ? $lng : null,
            'geocoded_at'      => ($lat !== '' && $lng !== '') ? date('Y-m-d H:i:s') : null,
            'cliente_id'       => $this->request->getPost('cliente_id') ?: null,
            'tecnico_id'       => $this->request->getPost('tecnico_id') ?: null,
            'data_pianificata' => $dataPianificata ?: null,
            'descrizione'      => $this->request->getPost('descrizione') ?: null,
            'note_interne'     => $this->request->getPost('note_interne') ?: null,
            'stato'            => $this->request->getPost('stato') ?: 'da_pianificare',
            'priorita'         => $this->request->getPost('priorita') ?: 'ordinario',
        ]);

        if ($richiestaId) {
            (new RichiestaModel())->update($richiestaId, ['stato' => 'in_lavorazione']);
        }

        return redirect()->to('interventi/' . $id)
            ->with('success', 'Intervento #' . $id . ' creato con successo.');
    }

    public function show(int $id)
    {
        $model     = new InterventoModel();
        $intervento = $model->select('interventi.*,
                c.ragsoc AS cliente_ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome, c.tipo AS cliente_tipo,
                c.lat AS cliente_lat, c.lng AS cliente_lng, c.email AS cliente_email,
                u_tec.nome AS tecnico_nome, u_tec.cognome AS tecnico_cognome,
                u_tec.telefono AS tecnico_telefono,
                r.richiedente AS richiesta_richiedente')
            ->join('clienti c', 'c.id = interventi.cliente_id', 'left')
            ->join('users u_tec', 'u_tec.id = interventi.tecnico_id', 'left')
            ->join('richieste_assistenza r', 'r.id = interventi.richiesta_id', 'left')
            ->find($id);

        if (! $intervento) {
            return redirect()->to('interventi')->with('error', 'Intervento non trovato.');
        }

        return view('interventi/show', [
            'title'      => 'Intervento #' . $id,
            'page_title' => 'Intervento #' . $id,
            'intervento' => $intervento,
            'tipi'       => TipoInterventoModel::comeLista(),
            'stati'      => InterventoModel::STATI,
        ]);
    }

    public function edit(int $id)
    {
        $model     = new InterventoModel();
        $intervento = $model->find($id);

        if (! $intervento) {
            return redirect()->to('interventi')->with('error', 'Intervento non trovato.');
        }

        $users     = new UserModel();
        $clientiModel = new ClienteModel();
        $richieste = new RichiestaModel();

        $richiesteAperte = $richieste
            ->select('richieste_assistenza.*, c.id AS cliente_id_resolved')
            ->join('clienti c', 'c.user_id = richieste_assistenza.user_id', 'left')
            ->where('richieste_assistenza.stato', 'nuova')
            ->orderBy('richieste_assistenza.created_at', 'DESC')
            ->findAll();

        $clientiList = $clientiModel->where('stato', 1)->orderBy('ragsoc, cognome')->findAll();

        // Se il cliente collegato è stato soft-deleted, aggiungilo in testa marcato
        if ($intervento['cliente_id']) {
            $inLista = array_search($intervento['cliente_id'], array_column($clientiList, 'id'));
            if ($inLista === false) {
                $clienteEliminato = (new ClienteModel())->withDeleted()->find($intervento['cliente_id']);
                if ($clienteEliminato) {
                    $clienteEliminato['_eliminato'] = true;
                    array_unshift($clientiList, $clienteEliminato);
                }
            }
        }

        return view('interventi/edit', [
            'title'      => 'Modifica Intervento #' . $id,
            'page_title' => 'Modifica Intervento',
            'intervento' => $intervento,
            'tecnici'    => $users->whereAssegnabile()->orderBy('cognome')->findAll(),
            'clienti'    => $clientiList,
            'richieste'  => $richiesteAperte,
            'tipi'       => TipoInterventoModel::comeLista(),
            'stati'      => InterventoModel::STATI,
        ]);
    }

    public function update(int $id)
    {
        $model     = new InterventoModel();
        $intervento = $model->find($id);

        if (! $intervento) {
            return redirect()->to('interventi')->with('error', 'Intervento non trovato.');
        }

        $rules = [
            'tipo_intervento'  => 'required|in_list[' . implode(',', array_keys(TipoInterventoModel::comeLista())) . ']',
            'stato'            => 'required|in_list[' . implode(',', array_keys(InterventoModel::STATI)) . ']',
            'cliente_id'       => 'permit_empty|is_natural_no_zero',
            'tecnico_id'       => 'permit_empty|is_natural_no_zero',
            'data_pianificata' => 'permit_empty|valid_date[Y-m-d\TH:i]',
            'descrizione'      => 'permit_empty|max_length[3000]',
            'note_interne'     => 'permit_empty|max_length[3000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataPianificata = $this->request->getPost('data_pianificata');
        if ($dataPianificata) {
            $dataPianificata = date('Y-m-d H:i:s', strtotime($dataPianificata));
        }

        $lat = $this->request->getPost('lat');
        $lng = $this->request->getPost('lng');

        $data = [
            'tipo_intervento'  => $this->request->getPost('tipo_intervento'),
            'luogo_intervento' => $this->request->getPost('luogo_intervento') ?: null,
            'citta'            => $this->request->getPost('citta') ?: null,
            'lat'              => $lat !== '' ? $lat : null,
            'lng'              => $lng !== '' ? $lng : null,
            'geocoded_at'      => ($lat !== '' && $lng !== '') ? date('Y-m-d H:i:s') : null,
            'cliente_id'       => $this->request->getPost('cliente_id') ?: null,
            'tecnico_id'       => $this->request->getPost('tecnico_id') ?: null,
            'data_pianificata' => $dataPianificata ?: null,
            'descrizione'      => $this->request->getPost('descrizione') ?: null,
            'note_interne'     => $this->request->getPost('note_interne') ?: null,
            'stato'            => $this->request->getPost('stato'),
        ];

        if ($data['stato'] === 'completato' && ! $intervento['data_completamento']) {
            $data['data_completamento'] = date('Y-m-d H:i:s');
        }

        $nuovaRichiesta = $this->request->getPost('richiesta_id') ?: null;
        $vecchiaRichiesta = $intervento['richiesta_id'] ?: null;

        if ($nuovaRichiesta !== $vecchiaRichiesta) {
            $data['richiesta_id'] = $nuovaRichiesta;
            $richiestaModel = new RichiestaModel();
            if ($nuovaRichiesta) {
                $richiestaModel->update($nuovaRichiesta, ['stato' => 'in_lavorazione']);
            }
            if ($vecchiaRichiesta) {
                $richiestaModel->update($vecchiaRichiesta, ['stato' => 'nuova']);
            }
        }

        $model->update($id, $data);

        $from = $this->request->getPost('from');
        $dest = $from ? base_url($from) : base_url('interventi/' . $id);
        return redirect()->to($dest)->with('success', 'Intervento aggiornato.');
    }

    public function delete(int $id)
    {
        $model = new InterventoModel();
        $model->delete($id);
        return redirect()->to('interventi')->with('success', 'Intervento eliminato.');
    }

    public function chiudi(int $id)
    {
        $model = new InterventoModel();
        $data  = [
            'stato'              => 'completato',
            'data_completamento' => date('Y-m-d H:i:s'),
        ];

        $note = trim($this->request->getPost('note_chiusura') ?? '');
        if ($note !== '') {
            $data['note_chiusura'] = $note;
        }

        $firmaBase64  = $this->request->getPost('firma_tecnico_data');
        $presaVisione = (bool) $this->request->getPost('presa_visione_tecnico');

        if ($firmaBase64 && str_starts_with($firmaBase64, 'data:image/png;base64,')) {
            $data['firma_tecnico']    = $firmaBase64;
            $data['firma_tecnico_at'] = date('Y-m-d H:i:s');
        } elseif ($presaVisione) {
            $data['firma_tecnico']    = 'presa_visione';
            $data['firma_tecnico_at'] = date('Y-m-d H:i:s');
        }

        $model->update($id, $data);
        $from = $this->request->getPost('from') ?: 'interventi/' . $id;
        return redirect()->to($from)->with('success', 'Intervento chiuso.');
    }

    public function assegnaTecnico(int $id)
    {
        $model     = new InterventoModel();
        $intervento = $model->find($id);

        if (! $intervento) {
            return redirect()->back()->with('error', 'Intervento non trovato.');
        }

        $tecnicoId = $this->request->getPost('tecnico_id') ?: null;
        $model->update($id, ['tecnico_id' => $tecnicoId]);

        if ($tecnicoId && $intervento['richiesta_id']) {
            (new RichiestaModel())->update($intervento['richiesta_id'], ['stato' => 'in_lavorazione']);
        }

        $from = $this->request->getPost('from') ?: 'interventi/' . $id;
        return redirect()->to($from)->with('success', 'Tecnico assegnato.');
    }

    public function pdf(int $id)
    {
        $dati = $this->_datiRapportino($id);
        if (! $dati) {
            return redirect()->to('interventi')->with('error', 'Intervento non trovato.');
        }
        if ($dati['intervento']['stato'] !== 'completato') {
            return redirect()->to('interventi/' . $id)->with('error', 'Il rapportino è disponibile solo per gli interventi completati.');
        }
        $html = view('interventi/pdf_rapportino', $dati);

        $opt = new Options();
        $opt->set('defaultFont', 'DejaVu Sans');
        $opt->set('isRemoteEnabled', false);
        $pdf = new Dompdf($opt);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $download = $this->response->download('rapportino-' . $id . '.pdf', $pdf->output(), true);
        $download->inline();

        return $download;
    }

    public function inviaEmail(int $id)
    {
        $dati = $this->_datiRapportino($id);
        if (! $dati) {
            return redirect()->back()->with('error', 'Intervento non trovato.');
        }
        if ($dati['intervento']['stato'] !== 'completato') {
            return redirect()->back()->with('error', 'Il rapportino è disponibile solo per gli interventi completati.');
        }

        $emailCliente = $dati['intervento']['cliente_email'] ?? '';
        $emailDest    = trim($this->request->getPost('email_destinatario') ?: $emailCliente);
        if (! $emailDest) {
            return redirect()->back()->with('error', 'Nessun indirizzo email disponibile per questo cliente.');
        }

        $html = view('interventi/pdf_rapportino', $dati);

        $opt = new Options();
        $opt->set('defaultFont', 'DejaVu Sans');
        $opt->set('isRemoteEnabled', false);
        $pdf = new Dompdf($opt);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $pdfOutput = $pdf->output();
        $tmpFile   = WRITEPATH . 'cache/rapportino_' . $id . '_' . uniqid() . '.pdf';
        $written   = file_put_contents($tmpFile, $pdfOutput);
        log_message('debug', 'PDF rapportino #' . $id . ': ' . strlen($pdfOutput) . ' byte generati, ' . $written . ' byte scritti in ' . $tmpFile . ', valido=' . (substr($pdfOutput, 0, 4) === '%PDF' ? 'SI' : 'NO'));

        $aziendaNome = setting('Azienda.sede_nome') ?: 'Colombini Piscine';
        $emailFrom   = config('Email')->fromEmail ?: 'noreply@colombinipiscine.it';

        $mailer = \Config\Services::email();
        $mailer->setNewline("\r\n");
        $mailer->setCRLF("\r\n");
        $mailer->setFrom($emailFrom, $aziendaNome);
        $mailer->setTo($emailDest);
        if ($emailFrom !== $emailDest) {
            $mailer->setCC($emailFrom);
        }
        $logoPath = setting('Azienda.sede_logo_path') ?? '';
        $logoSrc  = $logoPath ? base_url($logoPath) : '';

        $firma = view('email/firma', [
            'aziendaNome' => $aziendaNome,
            'telefono'    => setting('Azienda.sede_telefono') ?? '',
            'sito'        => setting('Azienda.sede_sito') ?? '',
            'indirizzo'   => trim((setting('Azienda.sede_indirizzo') ?? '') . ' — ' . (setting('Azienda.sede_citta') ?? ''), ' —'),
            'logoSrc'     => $logoSrc,
        ], ['debug' => false]);

        $mailer->setMailType('html');
        $mailer->setSubject('Rapportino intervento #' . $id . ' - ' . $dati['nomeCliente'] . ' - ' . $aziendaNome);
        $mailer->setMessage(view('email/rapportino', [
            'nomeCliente'  => $dati['nomeCliente'],
            'interventoId' => $id,
            'firma'        => $firma,
        ], ['debug' => false]));
        $mailer->attach($tmpFile, 'attachment', 'rapportino-' . $id . '.pdf');

        $inviato = $mailer->send(false);
        @unlink($tmpFile);

        if ($inviato) {
            return redirect()->back()->with('success', 'Rapportino inviato a ' . esc($emailDest) . '.');
        }

        $debug = $mailer->printDebugger(['headers']);
        $errMsg = 'Errore nell\'invio email.';
        if (str_contains($debug, 'fsockopen') || str_contains($debug, 'Unable to connect')) {
            $errMsg .= ' Impossibile raggiungere il server SMTP — verifica la connettività di rete o il firewall.';
        } else {
            $errMsg .= ' Verifica la configurazione SMTP nelle impostazioni.';
        }
        log_message('error', 'inviaEmail #' . $id . ' debug: ' . $debug);
        return redirect()->back()->with('error', $errMsg);
    }

    public function salvaFirma(int $id)
    {
        $model     = new InterventoModel();
        $intervento = $model->find($id);

        if (! $intervento || $intervento['stato'] !== 'completato') {
            return redirect()->to('interventi/' . $id)->with('error', 'Firma non consentita per questo intervento.');
        }

        $firmaBase64  = $this->request->getPost('firma_data');
        $presaVisione = (bool) $this->request->getPost('presa_visione');

        if ($firmaBase64 && str_starts_with($firmaBase64, 'data:image/png;base64,')) {
            $valore  = $firmaBase64;
            $success = 'Firma raccolta correttamente.';
        } elseif ($presaVisione) {
            $valore  = 'presa_visione';
            $success = 'Presa visione registrata.';
        } else {
            return redirect()->to('interventi/' . $id)->with('error', 'Firma non valida.');
        }

        $model->update($id, [
            'firma_cliente' => $valore,
            'firma_at'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('interventi/' . $id)->with('success', $success);
    }

    public function pianificaRapido(int $id)
    {
        $model     = new InterventoModel();
        $intervento = $model->find($id);

        if (! $intervento || $intervento['stato'] !== 'da_pianificare') {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msg' => 'Intervento non valido.']);
        }

        $dataPianificata = $this->request->getPost('data_pianificata');
        $tecnicoId       = $this->request->getPost('tecnico_id') ?: null;

        if (! $dataPianificata) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msg' => 'Data mancante.']);
        }

        $model->update($id, [
            'stato'            => 'pianificato',
            'data_pianificata' => date('Y-m-d H:i:s', strtotime($dataPianificata)),
            'tecnico_id'       => $tecnicoId,
        ]);

        return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
    }

    public function annullaPianificazione(int $id): ResponseInterface
    {
        $model     = new InterventoModel();
        $intervento = $model->find($id);

        if (! $intervento || in_array($intervento['stato'], ['completato', 'annullato'])) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msg' => 'Impossibile annullare la pianificazione.']);
        }

        $model->update($id, [
            'stato'            => 'da_pianificare',
            'tecnico_id'       => null,
            'data_pianificata' => null,
        ]);

        return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
    }

    // Suggerisce il tecnico più adatto in ordine di priorità:
    // 1. Referente (livello 3) meno occupato nel giorno
    // 2. Chi ha più storico completato su quel tipo (per cliente, poi generico)
    // 3. Primo disponibile con competenza >= 2 nel giorno
    public function apiTecnicoConsigliato(): ResponseInterface
    {
        $tipo      = $this->request->getGet('tipo_intervento') ?? '';
        $clienteId = (int) ($this->request->getGet('cliente_id') ?? 0);
        $data      = $this->request->getGet('data') ?? '';

        $model   = new InterventoModel();
        $tecnico = null;
        $source  = null;

        if ($data) {
            $tecnico = $model->tecnicoReferente($tipo, $data);
            if ($tecnico) $source = 'referente';
        }

        if (!$tecnico) {
            $tecnico = $model->tecnicoConsigliato($tipo, $clienteId);
            if ($tecnico) $source = 'storico';
        }

        if (!$tecnico && $data) {
            $tecnico = $model->tecnicoMenoOccupato($tipo, $data);
            if ($tecnico) $source = 'disponibilita';
        }

        return $this->response->setJSON(['tecnico' => $tecnico, 'source' => $source]);
    }

    public function apiOrarioSuggerito(): ResponseInterface
    {
        $tecnicoId = (int) ($this->request->getGet('tecnico_id') ?? 0);
        $data      = $this->request->getGet('data') ?? date('Y-m-d');

        $db          = db_connect();
        $oraInizio   = '08:00';
        $pausaInizio = null;
        $pausaFine   = null;

        if ($tecnicoId) {
            $giorno = (int) date('N', strtotime($data)); // 1=Lun, 7=Dom
            $orario = $db->table('tecnici_orari')
                ->where('tecnico_id', $tecnicoId)
                ->where('giorno', $giorno)
                ->where('attivo', 1)
                ->get()->getRowArray();

            if ($orario) {
                $oraInizio   = substr($orario['ora_inizio'], 0, 5);
                $pausaInizio = $orario['pausa_inizio'] ? substr($orario['pausa_inizio'], 0, 5) : null;
                $pausaFine   = $orario['pausa_fine']   ? substr($orario['pausa_fine'],   0, 5) : null;
            }

            $esistenti = $db->table('interventi i')
                ->select('i.data_pianificata, COALESCE(tp.durata_default, 60) AS durata')
                ->join('tipi_intervento tp', 'tp.codice = i.tipo_intervento', 'left')
                ->whereIn('i.stato', ['pianificato', 'in_corso'])
                ->where('i.tecnico_id', $tecnicoId)
                ->where('DATE(i.data_pianificata)', $data)
                ->where('i.deleted_at IS NULL', null, false)
                ->orderBy('i.data_pianificata', 'ASC')
                ->get()->getResultArray();
        } else {
            $esistenti = [];
        }

        $tSuggerito = strtotime($data . ' ' . $oraInizio . ':00');

        foreach ($esistenti as $inv) {
            $tFine = strtotime($inv['data_pianificata']) + ((int) $inv['durata'] * 60);
            if ($tFine > $tSuggerito) {
                $tSuggerito = $tFine;
            }
        }

        // Se cade nella pausa, sposta dopo la fine pausa
        if ($pausaInizio && $pausaFine) {
            $tPausaIn  = strtotime($data . ' ' . $pausaInizio . ':00');
            $tPausaOut = strtotime($data . ' ' . $pausaFine . ':00');
            if ($tSuggerito >= $tPausaIn && $tSuggerito < $tPausaOut) {
                $tSuggerito = $tPausaOut;
            }
        }

        return $this->response->setJSON([
            'ora'    => date('H:i', $tSuggerito),
            'n_prev' => count($esistenti),
        ]);
    }

    private function _datiRapportino(int $id): ?array
    {
        $model      = new InterventoModel();
        $intervento = $model->select('interventi.*,
                c.ragsoc AS cliente_ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome,
                c.email AS cliente_email,
                u_tec.nome AS tecnico_nome, u_tec.cognome AS tecnico_cognome')
            ->join('clienti c', 'c.id = interventi.cliente_id', 'left')
            ->join('users u_tec', 'u_tec.id = interventi.tecnico_id', 'left')
            ->find($id);

        if (! $intervento) return null;

        $nomeCliente = $intervento['cliente_ragsoc']
            ?: trim(($intervento['cliente_cognome'] ?? '') . ' ' . ($intervento['cliente_nome'] ?? ''))
            ?: '—';
        $nomeTecnico = $intervento['tecnico_nome']
            ? trim(($intervento['tecnico_cognome'] ?? '') . ' ' . $intervento['tecnico_nome'])
            : 'Non assegnato';

        $logoPath   = setting('Azienda.sede_logo_path') ?? '';
        $logoBase64 = '';
        if ($logoPath && is_file(FCPATH . $logoPath)) {
            $mime       = mime_content_type(FCPATH . $logoPath) ?: 'image/png';
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents(FCPATH . $logoPath));
        }

        return [
            'intervento'  => $intervento,
            'nomeCliente' => $nomeCliente,
            'nomeTecnico' => $nomeTecnico,
            'tipi'        => TipoInterventoModel::comeLista(),
            'stati'       => InterventoModel::STATI,
            'azienda'     => [
                'nome'      => setting('Azienda.sede_nome')      ?? '',
                'indirizzo' => setting('Azienda.sede_indirizzo') ?? '',
                'citta'     => setting('Azienda.sede_citta')     ?? '',
                'cap'       => setting('Azienda.sede_cap')       ?? '',
                'logo'      => $logoBase64,
            ],
        ];
    }
}
