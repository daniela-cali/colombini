<?php

namespace App\Controllers;

use App\Models\TipoInterventoModel;
use App\Models\VeicoloModel;

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
            'icona'          => $this->request->getPost('icona') ?: 'fa-tools',
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
            'icona'          => $this->request->getPost('icona') ?: 'fa-tools',
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
        $tipo  = $model->find($id);

        if (! $tipo) {
            return redirect()->to('sistema/tipi-intervento')->with('error', 'Tipo non trovato.');
        }

        $usati = (new \App\Models\InterventoModel())
            ->where('tipo_intervento', $tipo['codice'])
            ->countAllResults();

        if ($usati > 0) {
            return redirect()->to('sistema/tipi-intervento')
                ->with('error', "Impossibile eliminare «{$tipo['nome']}»: è usato da {$usati} intervento/i. Disattivalo invece di eliminarlo.");
        }

        $model->delete($id);

        return redirect()->to('sistema/tipi-intervento')
            ->with('success', 'Tipo intervento eliminato.');
    }

    // ─── Veicoli ──────────────────────────────────────────────────────────────

    public function veicoli(): string
    {
        $model = new VeicoloModel();

        return view('sistema/veicoli/index', [
            'title'      => 'Veicoli Aziendali',
            'page_title' => 'Veicoli Aziendali',
            'veicoli'    => $model->orderBy('nome')->findAll(),
        ]);
    }

    public function veicoliCreate(): string
    {
        return view('sistema/veicoli/create', [
            'title'      => 'Nuovo Veicolo',
            'page_title' => 'Nuovo Veicolo',
            'tipi'       => VeicoloModel::TIPI,
        ]);
    }

    public function veicoliStore()
    {
        $rules = [
            'nome'  => 'required|max_length[100]',
            'targa' => 'required|min_length[4]|max_length[20]|is_unique[veicoli.targa]',
        ];

        $messages = [
            'targa' => ['is_unique' => 'Questa targa è già presente.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new VeicoloModel();
        $model->insert([
            'nome'              => $this->request->getPost('nome'),
            'tipo'              => $this->request->getPost('tipo') ?: null,
            'targa'             => strtoupper($this->request->getPost('targa')),
            'cambio_automatico' => (int) ($this->request->getPost('cambio_automatico') ?? 0),
            'carico_massimo'    => $this->request->getPost('carico_massimo') !== '' ? (int) $this->request->getPost('carico_massimo') : null,
            'attivo'            => 1,
        ]);

        return redirect()->to('sistema/veicoli')->with('success', 'Veicolo aggiunto.');
    }

    public function veicoliEdit(int $id)
    {
        $model   = new VeicoloModel();
        $veicolo = $model->find($id);

        if (! $veicolo) {
            return redirect()->to('sistema/veicoli')->with('error', 'Veicolo non trovato.');
        }

        return view('sistema/veicoli/edit', [
            'title'      => 'Modifica Veicolo',
            'page_title' => 'Modifica Veicolo',
            'veicolo'    => $veicolo,
            'tipi'       => VeicoloModel::TIPI,
        ]);
    }

    public function veicoliUpdate(int $id)
    {
        $model   = new VeicoloModel();
        $veicolo = $model->find($id);

        if (! $veicolo) {
            return redirect()->to('sistema/veicoli')->with('error', 'Veicolo non trovato.');
        }

        $rules = [
            'nome'  => 'required|max_length[100]',
            'targa' => "required|min_length[4]|max_length[20]|is_unique[veicoli.targa,id,{$id}]",
        ];

        $messages = [
            'targa' => ['is_unique' => 'Questa targa è già presente.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'nome'              => $this->request->getPost('nome'),
            'tipo'              => $this->request->getPost('tipo') ?: null,
            'targa'             => strtoupper($this->request->getPost('targa')),
            'cambio_automatico' => (int) ($this->request->getPost('cambio_automatico') ?? 0),
            'carico_massimo'    => $this->request->getPost('carico_massimo') !== '' ? (int) $this->request->getPost('carico_massimo') : null,
            'attivo'            => (int) ($this->request->getPost('attivo') ?? 1),
        ]);

        return redirect()->to('sistema/veicoli')->with('success', 'Veicolo aggiornato.');
    }

    public function veicoliDelete(int $id)
    {
        $model   = new VeicoloModel();
        $veicolo = $model->find($id);

        if (! $veicolo) {
            return redirect()->to('sistema/veicoli')->with('error', 'Veicolo non trovato.');
        }

        $model->delete($id);

        return redirect()->to('sistema/veicoli')->with('success', 'Veicolo eliminato.');
    }
}
