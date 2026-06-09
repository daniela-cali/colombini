<?php

namespace App\Controllers\Impostazioni;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\TipoInterventoModel;
use App\Services\GeocoderService;
use CodeIgniter\HTTP\ResponseInterface;

class Generale extends BaseController
{
    public function index(): string
    {
        return view('impostazioni/index', ['title' => 'Impostazioni', 'page_title' => 'Impostazioni']);
    }

    public function update()
    {
        return redirect()->to('impostazioni');
    }

    public function parametri(): string
    {
        return view('impostazioni/parametri', [
            'title'      => 'Parametri Generali',
            'page_title' => 'Parametri Generali',
            'tipi'       => (new TipoInterventoModel())->where('attivo', 1)->orderBy('ordine')->findAll(),
        ]);
    }

    public function salvaParametri()
    {
        $post = $this->request->getPost();

        foreach (['sede_nome', 'sede_indirizzo', 'sede_citta', 'sede_cap', 'sede_lat', 'sede_lng', 'sede_telefono', 'sede_sito'] as $key) {
            setting()->set('Azienda.' . $key, $post[$key] ?? null);
        }

        $logo = $this->request->getFile('sede_logo');
        if ($logo && $logo->isValid() && ! $logo->hasMoved()) {
            $dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
            if (! is_dir($dir)) mkdir($dir, 0755, true);
            $ext      = $logo->getClientExtension();
            $filename = 'logo_azienda.' . $ext;
            $logo->move($dir, $filename, true);
            setting()->set('Azienda.sede_logo_path', 'uploads/' . $filename);
        }

        foreach (['orario_inizio', 'orario_fine', 'pausa_inizio', 'pausa_fine'] as $key) {
            setting()->set('Tecnici.' . $key, $post[$key] ?? null);
        }

        foreach (array_keys(TipoInterventoModel::comeLista()) as $tipo) {
            $val = $post['durata_' . $tipo] ?? null;
            setting()->set('Interventi.durata_' . $tipo, $val !== null && $val !== '' ? (int) $val : null);
        }

        return redirect()->to('impostazioni/parametri')->with('success', 'Impostazioni salvate.');
    }

    public function geocodifica(): string
    {
        $clienti = new ClienteModel();

        $totale    = $clienti->where('stato', 1)->countAllResults();
        $geocodati = $clienti->where('stato', 1)->where('geocoded_at IS NOT NULL')->countAllResults();
        $falliti   = $clienti->where('stato', 1)->where('geocoded_at IS NULL')->where('geocodifica_fallita', 1)->countAllResults();
        $daFare    = $clienti->where('stato', 1)->where('geocoded_at IS NULL')->where('geocodifica_fallita', 0)->countAllResults();

        $clientiFalliti = (new ClienteModel())
            ->where('stato', 1)
            ->where('geocoded_at IS NULL')
            ->where('geocodifica_fallita', 1)
            ->orderBy('ragsoc, cognome')
            ->findAll();

        return view('impostazioni/geocodifica', [
            'title'           => 'Geocodifica Clienti',
            'page_title'      => 'Geocodifica Clienti',
            'totale'          => $totale,
            'geocodati'       => $geocodati,
            'falliti'         => $falliti,
            'da_fare'         => $daFare,
            'clienti_falliti' => $clientiFalliti,
        ]);
    }

    public function geocodificaStep(): ResponseInterface
    {
        $force    = (bool) $this->request->getPost('force');
        $afterId  = (int)  $this->request->getPost('after_id');
        $clienti  = new ClienteModel();
        $geocoder = new GeocoderService();

        $query = $clienti->where('stato', 1)->where('geocoded_at IS NULL');
        if (! $force) {
            $query->where('geocodifica_fallita', 0);
        }
        if ($afterId) {
            $query->where('id >', $afterId);
        }

        $cliente = $query->orderBy('id', 'ASC')->first();

        if (! $cliente) {
            return $this->response->setJSON(['done' => true]);
        }

        $coords = $geocoder->geocode(
            $cliente['indirizzo'] ?? '',
            $cliente['citta']     ?? '',
            $cliente['cap']       ?? ''
        );

        $rimanenti = $clienti->where('stato', 1)
                             ->where('geocoded_at IS NULL')
                             ->where('geocodifica_fallita', 0)
                             ->countAllResults();

        if ($coords) {
            $clienti->update($cliente['id'], [
                'lat'                 => $coords['lat'],
                'lng'                 => $coords['lng'],
                'geocoded_at'         => date('Y-m-d H:i:s'),
                'geocodifica_fallita' => 0,
            ]);
            $esito = 'ok';
        } else {
            $clienti->update($cliente['id'], [
                'geocodifica_fallita' => 1,
            ]);
            $esito     = 'fallito';
            $rimanenti = max(0, $rimanenti - 1);
        }

        $nomeDisplay = $cliente['tipo'] === 'persona_fisica'
            ? trim(($cliente['cognome'] ?? '') . ' ' . ($cliente['nome'] ?? ''))
            : ($cliente['ragsoc'] ?? '');

        return $this->response->setJSON([
            'done'      => false,
            'esito'     => $esito,
            'cliente'   => $nomeDisplay,
            'rimanenti' => $rimanenti,
            'after_id'  => (int) $cliente['id'],
        ]);
    }
}
