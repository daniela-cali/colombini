<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\InterventoModel;
use App\Models\UserModel;
use App\Models\RichiestaModel;

class Interventi extends BaseController
{
    public function index(): string
    {
        $model = new InterventoModel();

        return view('interventi/index', [
            'title'      => 'Interventi',
            'page_title' => 'Interventi',
            'interventi' => $model->conDettagli(),
            'tipi'       => InterventoModel::TIPI,
            'stati'      => InterventoModel::STATI,
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
            'tecnici'          => $users->where('ruolo', 'tecnico')->orderBy('cognome')->findAll(),
            'clienti'          => $clienti->where('stato', 1)->orderBy('ragsoc, cognome')->findAll(),
            'richieste'        => $richiesteList,
            'tipi'             => InterventoModel::TIPI,
            'tecnico_id_pre'   => $tecnico_id_pre,
            'richiesta_id_pre' => $richiesta_id_pre,
        ]);
    }

    public function store()
    {
        $rules = [
            'tipo_intervento' => 'required|in_list[' . implode(',', array_keys(InterventoModel::TIPI)) . ']',
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

        $model = new InterventoModel();
        $id    = $model->insert([
            'richiesta_id'     => $richiestaId,
            'tipo_intervento'  => $this->request->getPost('tipo_intervento'),
            'luogo_intervento' => $this->request->getPost('luogo_intervento') ?: null,
            'cliente_id'       => $this->request->getPost('cliente_id') ?: null,
            'tecnico_id'       => $this->request->getPost('tecnico_id') ?: null,
            'data_pianificata' => $dataPianificata ?: null,
            'descrizione'      => $this->request->getPost('descrizione') ?: null,
            'note_interne'     => $this->request->getPost('note_interne') ?: null,
            'stato'            => 'pianificato',
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
                u_tec.nome AS tecnico_nome, u_tec.cognome AS tecnico_cognome,
                u_tec.telefono AS tecnico_telefono')
            ->join('clienti c', 'c.id = interventi.cliente_id', 'left')
            ->join('users u_tec', 'u_tec.id = interventi.tecnico_id', 'left')
            ->find($id);

        if (! $intervento) {
            return redirect()->to('interventi')->with('error', 'Intervento non trovato.');
        }

        return view('interventi/show', [
            'title'      => 'Intervento #' . $id,
            'page_title' => 'Intervento #' . $id,
            'intervento' => $intervento,
            'tipi'       => InterventoModel::TIPI,
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
        $clienti   = new ClienteModel();
        $richieste = new RichiestaModel();

        $richiesteAperte = $richieste
            ->select('richieste_assistenza.*, c.id AS cliente_id_resolved')
            ->join('clienti c', 'c.user_id = richieste_assistenza.user_id', 'left')
            ->where('richieste_assistenza.stato', 'nuova')
            ->orderBy('richieste_assistenza.created_at', 'DESC')
            ->findAll();

        return view('interventi/edit', [
            'title'      => 'Modifica Intervento #' . $id,
            'page_title' => 'Modifica Intervento',
            'intervento' => $intervento,
            'tecnici'    => $users->where('ruolo', 'tecnico')->orderBy('cognome')->findAll(),
            'clienti'    => $clienti->where('stato', 1)->orderBy('ragsoc, cognome')->findAll(),
            'richieste'  => $richiesteAperte,
            'tipi'       => InterventoModel::TIPI,
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
            'tipo_intervento'  => 'required|in_list[' . implode(',', array_keys(InterventoModel::TIPI)) . ']',
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

        $data = [
            'tipo_intervento'  => $this->request->getPost('tipo_intervento'),
            'luogo_intervento' => $this->request->getPost('luogo_intervento') ?: null,
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

        return redirect()->to('interventi/' . $id)->with('success', 'Intervento aggiornato.');
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
        $model->update($id, [
            'stato'              => 'completato',
            'data_completamento' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to('interventi/' . $id)->with('success', 'Intervento chiuso.');
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

    public function pdf(int $id): string
    {
        return view('coming_soon', ['title' => 'Stampa Intervento', 'page_title' => 'Stampa Intervento']);
    }
}
