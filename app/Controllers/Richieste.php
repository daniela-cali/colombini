<?php

namespace App\Controllers;

use App\Models\RichiestaModel;
use App\Models\RichiestaMessaggioModel;

class Richieste extends BaseController
{
    // Elenco di tutte le richieste aperte (nuova + in_lavorazione),
    // ordinate dalla più vecchia per evidenziare quelle in attesa.
    public function index(): string
    {
        $richieste = (new RichiestaModel())->aperte();

        return view('richieste/index', [
            'title'      => 'Richieste',
            'page_title' => 'Richieste di assistenza',
            'richieste'  => $richieste,
            'stati'      => RichiestaModel::STATI,
        ]);
    }

    // Dettaglio di una singola richiesta con il thread dei messaggi;
    // include il form per rispondere e cambiare stato.
    public function show(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $model     = new RichiestaModel();
        $richiesta = $model->select(
                                'richieste_assistenza.id, richieste_assistenza.user_id,
                                 richieste_assistenza.tipo_intervento, richieste_assistenza.luogo_intervento,
                                 richieste_assistenza.richiedente, richieste_assistenza.telefono_contatto,
                                 richieste_assistenza.note, richieste_assistenza.stato,
                                 richieste_assistenza.created_at,
                                 u.username, c.ragsoc, c.nome AS cliente_nome,
                                 c.cognome AS cliente_cognome, c.tipo AS cliente_tipo'
                           )
                           ->join('users u', 'u.id = richieste_assistenza.user_id', 'left')
                           ->join('clienti c', 'c.user_id = richieste_assistenza.user_id', 'left')
                           ->where('richieste_assistenza.id', $id)
                           ->first();

        if (! $richiesta) {
            return redirect()->to('richieste')->with('error', 'Richiesta non trovata.');
        }

        $messaggi = (new RichiestaMessaggioModel())->perRichiesta($id);

        return view('richieste/show', [
            'title'      => 'Richiesta #' . $id,
            'page_title' => 'Richiesta di assistenza',
            'richiesta'  => $richiesta,
            'messaggi'   => $messaggi,
            'stati'      => RichiestaModel::STATI,
        ]);
    }

    // Salva un messaggio di risposta nel thread e/o aggiorna lo stato della richiesta.
    // Il testo e lo stato sono indipendenti: si può agire su uno solo o su entrambi.
    public function storeRisposta(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $model     = new RichiestaModel();
        $richiesta = $model->find($id);

        if (! $richiesta) {
            return redirect()->to('richieste')->with('error', 'Richiesta non trovata.');
        }

        $testo = trim($this->request->getPost('testo') ?? '');
        $stato = $this->request->getPost('stato');

        if ($testo !== '') {
            (new RichiestaMessaggioModel())->insert([
                'richiesta_id' => $id,
                'user_id'      => auth()->id(),
                'testo'        => $testo,
            ]);
        }

        if ($stato && array_key_exists($stato, RichiestaModel::STATI)) {
            $model->update($id, ['stato' => $stato]);
        }

        return redirect()->to('richieste/' . $id)->with('success', 'Risposta salvata.');
    }
}
