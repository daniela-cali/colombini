<?php

namespace App\Controllers;

use App\Models\RichiestaModel;

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
            'tipi'       => RichiestaModel::TIPI,
            'stati'      => RichiestaModel::STATI,
        ]);
    }

    public function nuovaRichiesta(): string
    {
        return view('portale/nuova_richiesta', [
            'title'      => 'Nuova Richiesta',
            'page_title' => 'Nuova Richiesta di Assistenza',
            'tipi'       => RichiestaModel::TIPI,
        ]);
    }

    public function storeRichiesta()
    {
        $rules = [
            'tipo_intervento'  => 'required|in_list[' . implode(',', array_keys(RichiestaModel::TIPI)) . ']',
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

    public function show(int $id): string
    {
        $model    = new RichiestaModel();
        $richiesta = $model->where('user_id', auth()->id())->find($id);

        if (! $richiesta) {
            return redirect()->to('portale')->with('error', 'Richiesta non trovata.');
        }

        return view('portale/dettaglio_richiesta', [
            'title'      => 'Richiesta #' . $id,
            'page_title' => 'Dettaglio Richiesta',
            'richiesta'  => $richiesta,
            'tipi'       => RichiestaModel::TIPI,
            'stati'      => RichiestaModel::STATI,
        ]);
    }
}
