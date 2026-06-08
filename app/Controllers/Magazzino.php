<?php

namespace App\Controllers;

use App\Models\MagArticoliModel;
use App\Models\MagCategorieModel;
use App\Models\MagPosizioniModel;
use App\Models\MagMovimentiModel;
use App\Models\FornitoriModel;

class Magazzino extends BaseController
{
    // Elenco articoli con filtro opzionale per categoria, DataTables client-side.
    public function index(): string
    {
        $categoriaId = $this->request->getGet('categoria') ? (int) $this->request->getGet('categoria') : null;

        return view('magazzino/index', [
            'title'        => 'Magazzino',
            'page_title'   => 'Articoli',
            'articoli'     => (new MagArticoliModel())->elenco($categoriaId),
            'categorie'    => (new MagCategorieModel())->comeLista(),
            'categoria_id' => $categoriaId,
        ]);
    }

    // Scheda articolo con storico movimenti.
    public function show(int $id)
    {
        $model    = new MagArticoliModel();
        $articolo = $model->conDettagli($id);

        if (! $articolo) {
            return redirect()->to('magazzino')->with('error', 'Articolo non trovato.');
        }

        return view('magazzino/show', [
            'title'      => $articolo['descrizione'],
            'page_title' => 'Scheda Articolo',
            'articolo'   => $articolo,
            'storico'    => (new MagMovimentiModel())->perArticolo($id),
            'tipi'       => MagMovimentiModel::TIPI,
        ]);
    }

    // Form creazione nuovo articolo.
    public function create(): string
    {
        return view('magazzino/create', [
            'title'      => 'Nuovo Articolo',
            'page_title' => 'Nuovo Articolo',
            'categorie'  => (new MagCategorieModel())->comeLista(),
            'posizioni'  => (new MagPosizioniModel())->comeLista(),
            'fornitori'  => (new FornitoriModel())->comeLista(),
        ]);
    }

    // Salva nuovo articolo.
    public function store()
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new MagArticoliModel();
        $id    = $model->insert($this->request->getPost());

        return redirect()->to('magazzino/' . $id)->with('success', 'Articolo creato.');
    }

    // Form modifica articolo.
    public function edit(int $id)
    {
        $model    = new MagArticoliModel();
        $articolo = $model->find($id);

        if (! $articolo) {
            return redirect()->to('magazzino')->with('error', 'Articolo non trovato.');
        }

        return view('magazzino/edit', [
            'title'      => 'Modifica Articolo',
            'page_title' => 'Modifica Articolo',
            'articolo'   => $articolo,
            'categorie'  => (new MagCategorieModel())->comeLista(),
            'posizioni'  => (new MagPosizioniModel())->comeLista(),
            'fornitori'  => (new FornitoriModel())->comeLista(),
        ]);
    }

    // Aggiorna articolo.
    public function update(int $id)
    {
        $model    = new MagArticoliModel();
        $articolo = $model->find($id);

        if (! $articolo) {
            return redirect()->to('magazzino')->with('error', 'Articolo non trovato.');
        }

        if (! $this->validate($this->validationRules($id))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, $this->request->getPost());

        return redirect()->to('magazzino/' . $id)->with('success', 'Articolo aggiornato.');
    }

    // Elimina articolo (soft delete); bloccato se ha movimenti registrati.
    public function delete(int $id)
    {
        $model    = new MagArticoliModel();
        $articolo = $model->find($id);

        if (! $articolo) {
            return redirect()->to('magazzino')->with('error', 'Articolo non trovato.');
        }

        $count = $this->db->table('mag_movimenti_righe')
                          ->where('articolo_id', $id)
                          ->countAllResults();

        if ($count > 0) {
            return redirect()->to('magazzino/' . $id)
                ->with('error', "Impossibile eliminare: l'articolo ha {$count} movimento/i registrato/i.");
        }

        $model->delete($id);

        return redirect()->to('magazzino')->with('success', 'Articolo eliminato.');
    }

    // Regole di validazione; cod_articolo univoco con esclusione dell'id corrente in modifica.
    private function validationRules(?int $excludeId = null): array
    {
        $codRule = 'required|max_length[20]|is_unique[mag_articoli.cod_articolo'
            . ($excludeId ? ",id,{$excludeId}" : '') . ']';

        return [
            'cod_articolo'    => $codRule,
            'descrizione'     => 'required|max_length[500]',
            'unita_misura'    => 'permit_empty|max_length[10]',
            'prezzo_acquisto' => 'permit_empty|decimal',
            'prezzo_vendita'  => 'permit_empty|decimal',
        ];
    }
}
