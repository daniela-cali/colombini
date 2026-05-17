<?php

namespace App\Controllers;

use App\Models\ViaggioModel;
use App\Models\ViaggioTappaModel;
use App\Models\UserModel;
use App\Models\VeicoloModel;
use App\Models\InterventoModel;

class Viaggi extends BaseController
{
    public function index(): string
    {
        $data   = $this->request->getGet('data') ?? date('Y-m-d');
        $model  = new ViaggioModel();

        return view('viaggi/index', [
            'title'      => 'Viaggi',
            'page_title' => 'Viaggi',
            'data'       => $data,
            'viaggi'     => $model->perData($data),
            'stati'      => ViaggioModel::STATI,
        ]);
    }

    public function show(int $id): string
    {
        $model   = new ViaggioModel();
        $viaggio = $model->conTappe($id);

        if (! $viaggio) {
            return redirect()->to('viaggi')->with('error', 'Viaggio non trovato.');
        }

        return view('viaggi/show', [
            'title'      => 'Viaggio del ' . date('d/m/Y', strtotime($viaggio['data'])),
            'page_title' => 'Dettaglio Viaggio',
            'viaggio'    => $viaggio,
            'stati'      => ViaggioModel::STATI,
            'stati_int'  => InterventoModel::STATI,
            'priorita'   => InterventoModel::PRIORITA,
        ]);
    }

    public function autorizza(int $id)
    {
        $model   = new ViaggioModel();
        $viaggio = $model->find($id);

        if (! $viaggio || $viaggio['stato'] !== 'bozza') {
            return redirect()->to('viaggi/' . $id)->with('error', 'Operazione non consentita.');
        }

        $tappeModel    = new ViaggioTappaModel();
        $interventoModel = new InterventoModel();
        $tappe         = $tappeModel->where('viaggio_id', $id)->findAll();

        foreach ($tappe as $tappa) {
            $interventoModel->update($tappa['intervento_id'], [
                'tecnico_id'      => $viaggio['tecnico_id'],
                'data_pianificata' => $viaggio['data'],
                'stato'           => 'pianificato',
            ]);
        }

        $model->update($id, ['stato' => 'autorizzato']);

        return redirect()->to('viaggi/' . $id)->with('success', 'Viaggio autorizzato.');
    }

    public function annulla(int $id)
    {
        $model   = new ViaggioModel();
        $viaggio = $model->find($id);

        if (! $viaggio || ! in_array($viaggio['stato'], ['bozza', 'autorizzato'])) {
            return redirect()->to('viaggi/' . $id)->with('error', 'Operazione non consentita.');
        }

        $tappeModel      = new ViaggioTappaModel();
        $interventoModel = new InterventoModel();
        $tappe           = $tappeModel->where('viaggio_id', $id)->findAll();

        foreach ($tappe as $tappa) {
            $interventoModel->update($tappa['intervento_id'], [
                'tecnico_id'      => null,
                'data_pianificata' => null,
                'stato'           => 'da_pianificare',
            ]);
        }

        $tappeModel->where('viaggio_id', $id)->delete();
        $model->delete($id, true);

        return redirect()->to('viaggi')->with('success', 'Viaggio annullato e interventi rimessi in coda.');
    }
}
