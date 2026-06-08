<?php

namespace App\Controllers;

use App\Models\MagMovimentiModel;
use App\Models\MagMovimentiRigheModel;
use App\Models\MagArticoliModel;
use App\Models\FornitoriModel;

class MagMovimenti extends BaseController
{
    // Tipi gestibili manualmente; 'consegna' è generato dalla chiusura intervento.
    private const TIPI_FORM = ['carico', 'scarico', 'rettifica_p', 'rettifica_m', 'inventario'];

    // Lista movimenti con filtro opzionale per tipo e range di date.
    public function index(): string
    {
        $tipo = $this->request->getGet('tipo') ?: null;
        $dal  = $this->request->getGet('dal')  ?: null;
        $al   = $this->request->getGet('al')   ?: null;

        return view('magazzino/movimenti/index', [
            'title'      => 'Movimenti magazzino',
            'page_title' => 'Movimenti',
            'movimenti'  => (new MagMovimentiModel())->elenco($tipo, $dal, $al),
            'tipi'       => MagMovimentiModel::TIPI,
            'tipo_sel'   => $tipo,
            'dal'        => $dal,
            'al'         => $al,
        ]);
    }

    // Form nuovo movimento; passa articoli con fornitore_id per il filtro JS.
    public function create(): string
    {
        $articoli = (new MagArticoliModel())
            ->select('id, cod_articolo, descrizione, fornitore_id, prezzo_acquisto, perc_sconto_1, perc_sconto_2, perc_sconto_3')
            ->orderBy('descrizione')
            ->findAll();

        return view('magazzino/movimenti/create', [
            'title'      => 'Nuovo Movimento',
            'page_title' => 'Nuovo Movimento',
            'tipi'       => array_intersect_key(MagMovimentiModel::TIPI, array_flip(self::TIPI_FORM)),
            'articoli'   => $articoli,
            'fornitori'  => (new FornitoriModel())->comeLista(),
        ]);
    }

    // Salva movimento e aggiorna giacenze in transazione atomica.
    public function store()
    {
        $tipo  = $this->request->getPost('tipo');
        $righe = $this->request->getPost('righe') ?? [];

        if (! in_array($tipo, self::TIPI_FORM, true)) {
            return redirect()->back()->withInput()->with('error', 'Tipo movimento non valido.');
        }

        $righe = array_values(array_filter($righe, fn($r) => ! empty($r['articolo_id']) && (int) ($r['quantita'] ?? 0) > 0));

        if (empty($righe)) {
            return redirect()->back()->withInput()->with('error', 'Inserisci almeno una riga con articolo e quantità.');
        }

        $artIds = array_column($righe, 'articolo_id');
        if (count($artIds) !== count(array_unique($artIds))) {
            return redirect()->back()->withInput()->with('error', 'Lo stesso articolo non può comparire due volte nello stesso movimento.');
        }

        $isCarico = in_array($tipo, MagMovimentiModel::TIPI_CARICO);

        $db = \Config\Database::connect();
        $db->transStart();

        $movId = (new MagMovimentiModel())->insert([
            'tipo'          => $tipo,
            'data'          => $this->request->getPost('data') ?: date('Y-m-d'),
            'num_documento' => $this->request->getPost('num_documento') ?: null,
            'fornitore_id'  => $this->request->getPost('fornitore_id') ?: null,
            'note'          => $this->request->getPost('note')          ?: null,
            'user_id'       => auth()->id(),
        ]);

        $righeModel = new MagMovimentiRigheModel();
        $artModel   = new MagArticoliModel();

        foreach ($righe as $r) {
            $qty   = (int) $r['quantita'];
            $delta = $isCarico ? $qty : -$qty;

            $righeModel->insert([
                'movimento_id'  => $movId,
                'articolo_id'   => (int) $r['articolo_id'],
                'quantita'      => $qty,
                'costo_unitario'=> $isCarico ? (float) ($r['costo_unitario'] ?? 0) : 0,
            ]);

            $artModel->aggiornaGiacenza((int) $r['articolo_id'], $delta);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Errore nel salvataggio del movimento. Riprovare.');
        }

        return redirect()->to('magazzino/movimenti/' . $movId)->with('success', 'Movimento registrato correttamente.');
    }

    // Dettaglio movimento con righe.
    public function show(int $id)
    {
        $movimento = (new MagMovimentiModel())->conRighe($id);

        if (! $movimento) {
            return redirect()->to('magazzino/movimenti')->with('error', 'Movimento non trovato.');
        }

        return view('magazzino/movimenti/show', [
            'title'      => 'Movimento #' . $id,
            'page_title' => 'Dettaglio Movimento',
            'movimento'  => $movimento,
            'tipi'       => MagMovimentiModel::TIPI,
        ]);
    }

    // Annulla movimento: ripristina le giacenze in ordine inverso, poi elimina.
    public function delete(int $id)
    {
        $movimento = (new MagMovimentiModel())->conRighe($id);

        if (! $movimento) {
            return redirect()->to('magazzino/movimenti')->with('error', 'Movimento non trovato.');
        }

        $isCarico = in_array($movimento['tipo'], MagMovimentiModel::TIPI_CARICO);
        $artModel = new MagArticoliModel();

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($movimento['righe'] as $r) {
            // Delta inverso: se era un carico tolgo, se era uno scarico rimetto
            $delta = $isCarico ? -(int) $r['quantita'] : (int) $r['quantita'];
            $artModel->aggiornaGiacenza((int) $r['articolo_id'], $delta);
        }

        $db->table('mag_movimenti_righe')->where('movimento_id', $id)->delete();
        (new MagMovimentiModel())->delete($id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('magazzino/movimenti/' . $id)->with('error', 'Errore nell\'annullamento del movimento.');
        }

        return redirect()->to('magazzino/movimenti')->with('success', 'Movimento annullato e giacenze ripristinate.');
    }
}
