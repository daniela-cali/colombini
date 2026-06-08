<?php

namespace App\Controllers;

use App\Models\FornitoriModel;
use App\Models\MagArticoliModel;

class Fornitori extends BaseController
{
    // Elenco fornitori attivi con DataTables client-side.
    public function index(): string
    {
        $model     = new FornitoriModel();
        $fornitori = $model->where('attivo', 1)
                           ->orderBy('ragione_sociale, cognome')
                           ->findAll();

        return view('fornitori/index', [
            'title'      => 'Fornitori',
            'page_title' => 'Fornitori',
            'fornitori'  => $fornitori,
        ]);
    }

    // Scheda fornitore con elenco articoli collegati.
    public function show(int $id)
    {
        $model     = new FornitoriModel();
        $fornitore = $model->find($id);

        if (! $fornitore) {
            return redirect()->to('fornitori')->with('error', 'Fornitore non trovato.');
        }

        $articoli = (new MagArticoliModel())
            ->where('fornitore_id', $id)
            ->orderBy('descrizione')
            ->findAll();

        return view('fornitori/show', [
            'title'        => 'Scheda Fornitore',
            'page_title'   => 'Scheda Fornitore',
            'fornitore'    => $fornitore,
            'nome_display' => $model->getNomeDisplay($fornitore),
            'articoli'     => $articoli,
        ]);
    }

    // Form creazione nuovo fornitore.
    public function create(): string
    {
        return view('fornitori/create', [
            'title'      => 'Nuovo Fornitore',
            'page_title' => 'Nuovo Fornitore',
        ]);
    }

    // Salva nuovo fornitore.
    public function store()
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new FornitoriModel();
        $id    = $model->insert(array_merge($this->request->getPost(), ['attivo' => 1]));

        return redirect()->to('fornitori/' . $id)->with('success', 'Fornitore creato.');
    }

    // Form modifica fornitore.
    public function edit(int $id)
    {
        $model     = new FornitoriModel();
        $fornitore = $model->find($id);

        if (! $fornitore) {
            return redirect()->to('fornitori')->with('error', 'Fornitore non trovato.');
        }

        return view('fornitori/edit', [
            'title'      => 'Modifica Fornitore',
            'page_title' => 'Modifica Fornitore',
            'fornitore'  => $fornitore,
        ]);
    }

    // Aggiorna fornitore.
    public function update(int $id)
    {
        $model     = new FornitoriModel();
        $fornitore = $model->find($id);

        if (! $fornitore) {
            return redirect()->to('fornitori')->with('error', 'Fornitore non trovato.');
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, $this->request->getPost());

        return redirect()->to('fornitori/' . $id)->with('success', 'Fornitore aggiornato.');
    }

    // Elimina fornitore; bloccato se ha articoli collegati.
    public function delete(int $id)
    {
        $model     = new FornitoriModel();
        $fornitore = $model->find($id);

        if (! $fornitore) {
            return redirect()->to('fornitori')->with('error', 'Fornitore non trovato.');
        }

        $count = (new MagArticoliModel())->where('fornitore_id', $id)->countAllResults();
        if ($count > 0) {
            return redirect()->to('fornitori/' . $id)
                ->with('error', "Impossibile eliminare: il fornitore ha {$count} articolo/i collegato/i.");
        }

        $model->delete($id);

        return redirect()->to('fornitori')->with('success', 'Fornitore eliminato.');
    }

    // Regole di validazione.
    private function validationRules(): array
    {
        return [
            'ragione_sociale' => 'permit_empty|max_length[150]',
            'nome'            => 'permit_empty|max_length[100]',
            'cognome'         => 'permit_empty|max_length[100]',
            'email'           => 'permit_empty|valid_email',
            'piva'            => 'permit_empty|max_length[20]',
        ];
    }
}
