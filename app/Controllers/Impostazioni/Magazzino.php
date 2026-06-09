<?php

namespace App\Controllers\Impostazioni;

use App\Controllers\BaseController;
use App\Models\MagArticoliModel;
use App\Models\MagCategorieModel;
use App\Models\MagPosizioniModel;

class Magazzino extends BaseController
{
    // Elenco categorie; se ?edit=ID, passa la categoria in modifica al form.
    public function categorie(): string
    {
        $model  = new MagCategorieModel();
        $editId = (int) ($this->request->getGet('edit') ?? 0);

        return view('impostazioni/mag_categorie', [
            'title'           => 'Categorie Magazzino',
            'page_title'      => 'Categorie Magazzino',
            'categorie'       => $model->orderBy('ordine')->findAll(),
            'editing'         => $editId ? $model->find($editId) : null,
            'prossimo_ordine' => $model->selectMax('ordine')->first()['ordine'] + 1,
        ]);
    }

    // Salva nuova categoria.
    public function storeCategoria()
    {
        if (! $this->validate(['nome' => 'required|max_length[100]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new MagCategorieModel())->insert($this->request->getPost());

        return redirect()->to('impostazioni/mag-categorie')->with('success', 'Categoria aggiunta.');
    }

    // Form modifica categoria — reindirizza all'index con ?edit=ID.
    public function editCategoria(int $id)
    {
        return redirect()->to('impostazioni/mag-categorie?edit=' . $id);
    }

    // Aggiorna categoria esistente.
    public function updateCategoria(int $id)
    {
        $model = new MagCategorieModel();

        if (! $model->find($id)) {
            return redirect()->to('impostazioni/mag-categorie')->with('error', 'Categoria non trovata.');
        }

        if (! $this->validate(['nome' => 'required|max_length[100]'])) {
            return redirect()->to('impostazioni/mag-categorie?edit=' . $id)
                ->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, $this->request->getPost());

        return redirect()->to('impostazioni/mag-categorie')->with('success', 'Categoria aggiornata.');
    }

    // Elimina categoria; bloccato se ha articoli collegati.
    public function deleteCategoria(int $id)
    {
        $model = new MagCategorieModel();
        $count = (new MagArticoliModel())->where('categoria_id', $id)->countAllResults();

        if ($count > 0) {
            return redirect()->to('impostazioni/mag-categorie')
                ->with('error', "Impossibile eliminare: la categoria ha {$count} articolo/i collegato/i.");
        }

        $model->delete($id);

        return redirect()->to('impostazioni/mag-categorie')->with('success', 'Categoria eliminata.');
    }

    // -------------------------------------------------------------------------
    // Posizioni scaffale
    // -------------------------------------------------------------------------

    // Elenco posizioni; se ?edit=ID, passa la posizione in modifica al form.
    public function posizioni(): string
    {
        $model  = new MagPosizioniModel();
        $editId = (int) ($this->request->getGet('edit') ?? 0);

        return view('impostazioni/mag_posizioni', [
            'title'      => 'Posizioni Scaffale',
            'page_title' => 'Posizioni Scaffale',
            'posizioni'  => $model->orderBy('nome')->findAll(),
            'editing'    => $editId ? $model->find($editId) : null,
        ]);
    }

    // Salva nuova posizione.
    public function storePosizione()
    {
        if (! $this->validate(['nome' => 'required|max_length[50]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new MagPosizioniModel())->insert($this->request->getPost());

        return redirect()->to('impostazioni/mag-posizioni')->with('success', 'Posizione aggiunta.');
    }

    // Form modifica — reindirizza all'index con ?edit=ID.
    public function editPosizione(int $id)
    {
        return redirect()->to('impostazioni/mag-posizioni?edit=' . $id);
    }

    // Aggiorna posizione esistente.
    public function updatePosizione(int $id)
    {
        $model = new MagPosizioniModel();

        if (! $model->find($id)) {
            return redirect()->to('impostazioni/mag-posizioni')->with('error', 'Posizione non trovata.');
        }

        if (! $this->validate(['nome' => 'required|max_length[50]'])) {
            return redirect()->to('impostazioni/mag-posizioni?edit=' . $id)
                ->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, $this->request->getPost());

        return redirect()->to('impostazioni/mag-posizioni')->with('success', 'Posizione aggiornata.');
    }

    // Elimina posizione; bloccato se ha articoli collegati per evitare perdita silenziosa di dati.
    public function deletePosizione(int $id)
    {
        $model = new MagPosizioniModel();

        if (! $model->find($id)) {
            return redirect()->to('impostazioni/mag-posizioni')->with('error', 'Posizione non trovata.');
        }

        $count = (new MagArticoliModel())->where('posizione_id', $id)->countAllResults();

        if ($count > 0) {
            return redirect()->to('impostazioni/mag-posizioni')
                ->with('error', "Impossibile eliminare: {$count} articolo/i ha questa posizione assegnata. Riassegna prima la posizione degli articoli.");
        }

        $model->delete($id);

        return redirect()->to('impostazioni/mag-posizioni')->with('success', 'Posizione eliminata.');
    }
}
