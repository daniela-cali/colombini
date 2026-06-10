<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\RichiestaModel;
use App\Models\RichiestaMessaggioModel;
use App\Models\TipoInterventoModel;
use CodeIgniter\HTTP\RedirectResponse;

class Portale extends BaseController
{
    public function index(): string
    {
        $model    = new RichiestaModel();
        $richieste = $model->perCliente(auth()->id());

        return view('portale/dashboard', [
            'title'      => 'Area Cliente',
            'page_title' => 'Le mie richieste',
            'richieste'  => $richieste,
            'tipi'       => TipoInterventoModel::comeLista(),
            'stati'      => RichiestaModel::STATI,
        ]);
    }

    public function nuovaRichiesta(): string
    {
        $cliente = (new ClienteModel())->where('user_id', auth()->id())->first();

        $richiedenteDefault = '';
        if ($cliente) {
            $richiedenteDefault = $cliente['ragsoc']
                ?: trim(($cliente['cognome'] ?? '') . ' ' . ($cliente['nome'] ?? ''));
        }

        return view('portale/nuova_richiesta', [
            'title'               => 'Nuova Richiesta',
            'page_title'          => 'Nuova Richiesta di Assistenza',
            'tipi'                => TipoInterventoModel::comeListaCompleta(),
            'richiedente_default' => $richiedenteDefault,
            'telefono_default'    => $cliente['telefono'] ?? '',
        ]);
    }

    public function storeRichiesta()
    {
        $rules = [
            'tipo_intervento'  => 'required|in_list[' . implode(',', array_keys(TipoInterventoModel::comeLista())) . ']',
            'richiedente'      => 'required|max_length[100]',
            'telefono_contatto'=> 'required|max_length[30]',
            'note'             => 'required|max_length[2000]',
            'luogo_intervento' => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new RichiestaModel();
        $model->insert([
            'user_id'           => auth()->id(),
            'tipo_intervento'   => $this->request->getPost('tipo_intervento'),
            'luogo_intervento'  => $this->request->getPost('luogo_intervento') ?: null,
            'richiedente'       => $this->request->getPost('richiedente'),
            'telefono_contatto' => $this->request->getPost('telefono_contatto'),
            'note'              => $this->request->getPost('note'),
            'stato'             => 'nuova',
        ]);

        return redirect()->to('portale')
            ->with('success', 'Richiesta inviata correttamente. Vi contatteremo al più presto.');
    }

    public function show(int $id): string|RedirectResponse
    {
        $model    = new RichiestaModel();
        $richiesta = $model->where('user_id', auth()->id())->find($id);

        if (! $richiesta) {
            return redirect()->to('portale')->with('error', 'Richiesta non trovata.');
        }

        $messaggi = (new RichiestaMessaggioModel())->perRichiesta($id);

        return view('portale/dettaglio_richiesta', [
            'title'      => 'Richiesta #' . $id,
            'page_title' => 'Dettaglio Richiesta',
            'richiesta'  => $richiesta,
            'messaggi'   => $messaggi,
            'tipi'       => TipoInterventoModel::comeLista(),
            'stati'      => RichiestaModel::STATI,
        ]);
    }
}
