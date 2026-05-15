<?php

namespace App\Controllers;

use App\Models\TipoInterventoModel;

class Sistema extends BaseController
{
    // ─── Tipi Intervento ───────────────────────────────────────────────────────

    public function tipiIntervento(): string
    {
        $model = new TipoInterventoModel();

        return view('sistema/tipi_intervento/index', [
            'title'      => 'Tipi Intervento',
            'page_title' => 'Tipi Intervento',
            'tipi'       => $model->orderBy('ordine')->findAll(),
        ]);
    }

    public function tipiInterventoCreate(): string
    {
        return view('sistema/tipi_intervento/create', [
            'title'      => 'Nuovo Tipo Intervento',
            'page_title' => 'Nuovo Tipo Intervento',
        ]);
    }

    public function tipiInterventoStore()
    {
        $rules = [
            'codice'         => 'required|alpha_dash|max_length[50]|is_unique[tipi_intervento.codice]',
            'nome'           => 'required|max_length[100]',
            'durata_default' => 'required|is_natural_no_zero',
        ];

        $messages = [
            'codice' => ['is_unique' => 'Questo codice è già in uso.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new TipoInterventoModel();
        $model->insert([
            'codice'         => $this->request->getPost('codice'),
            'nome'           => $this->request->getPost('nome'),
            'durata_default' => (int) $this->request->getPost('durata_default'),
            'attivo'         => 1,
            'ordine'         => (int) ($this->request->getPost('ordine') ?: 0),
        ]);

        return redirect()->to('sistema/tipi-intervento')
            ->with('success', 'Tipo intervento creato.');
    }

    public function tipiInterventoEdit(int $id)
    {
        $model = new TipoInterventoModel();
        $tipo  = $model->find($id);

        if (! $tipo) {
            return redirect()->to('sistema/tipi-intervento')->with('error', 'Tipo non trovato.');
        }

        return view('sistema/tipi_intervento/edit', [
            'title'      => 'Modifica Tipo Intervento',
            'page_title' => 'Modifica Tipo Intervento',
            'tipo'       => $tipo,
        ]);
    }

    public function tipiInterventoUpdate(int $id)
    {
        $model = new TipoInterventoModel();
        $tipo  = $model->find($id);

        if (! $tipo) {
            return redirect()->to('sistema/tipi-intervento')->with('error', 'Tipo non trovato.');
        }

        $rules = [
            'codice'         => "required|alpha_dash|max_length[50]|is_unique[tipi_intervento.codice,id,{$id}]",
            'nome'           => 'required|max_length[100]',
            'durata_default' => 'required|is_natural_no_zero',
        ];

        $messages = [
            'codice' => ['is_unique' => 'Questo codice è già in uso.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'codice'         => $this->request->getPost('codice'),
            'nome'           => $this->request->getPost('nome'),
            'durata_default' => (int) $this->request->getPost('durata_default'),
            'attivo'         => (int) ($this->request->getPost('attivo') ?? 1),
            'ordine'         => (int) ($this->request->getPost('ordine') ?: 0),
        ]);

        return redirect()->to('sistema/tipi-intervento')
            ->with('success', 'Tipo intervento aggiornato.');
    }

    public function tipiInterventoDelete(int $id)
    {
        $model = new TipoInterventoModel();
        $model->delete($id);

        return redirect()->to('sistema/tipi-intervento')
            ->with('success', 'Tipo intervento eliminato.');
    }
}
