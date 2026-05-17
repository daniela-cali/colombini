<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ClienteModel;
use App\Models\TipoInterventoModel;
use App\Services\GeocoderService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Entities\User;

class Impostazioni extends BaseController
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

        // Sede aziendale
        foreach (['sede_nome', 'sede_indirizzo', 'sede_citta', 'sede_cap', 'sede_lat', 'sede_lng', 'sede_telefono', 'sede_sito'] as $key) {
            setting()->set('Azienda.' . $key, $post[$key] ?? null);
        }

        // Upload logo
        $logo = $this->request->getFile('sede_logo');
        if ($logo && $logo->isValid() && ! $logo->hasMoved()) {
            $dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
            if (! is_dir($dir)) mkdir($dir, 0755, true);
            $ext      = $logo->getClientExtension();
            $filename = 'logo_azienda.' . $ext;
            $logo->move($dir, $filename, true);
            setting()->set('Azienda.sede_logo_path', 'uploads/' . $filename);
        }

        // Orari default tecnici
        foreach (['orario_inizio', 'orario_fine', 'pausa_inizio', 'pausa_fine'] as $key) {
            setting()->set('Tecnici.' . $key, $post[$key] ?? null);
        }

        // Durate interventi
        foreach (array_keys(TipoInterventoModel::comeLista()) as $tipo) {
            $val = $post['durata_' . $tipo] ?? null;
            setting()->set('Interventi.durata_' . $tipo, $val !== null && $val !== '' ? (int) $val : null);
        }

        return redirect()->to('impostazioni/parametri')->with('success', 'Impostazioni salvate.');
    }

    public function utenti(): string
    {
        $users    = new UserModel();
        $clienti  = $users->where('ruolo', 'cliente')->orderBy('cognome', 'ASC')->findAll();

        return view('impostazioni/utenti', [
            'title'      => 'Utenti Portale',
            'page_title' => 'Utenti Portale',
            'clienti'    => $clienti,
        ]);
    }

    public function creaCliente(): string
    {
        return view('impostazioni/crea_cliente', [
            'title'      => 'Nuovo Utente Portale',
            'page_title' => 'Nuovo Utente Portale',
        ]);
    }

    public function storeCliente()
    {
        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'nome'             => 'required|max_length[100]',
            'cognome'          => 'required|max_length[100]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'username'         => ['is_unique' => 'Questo nome utente è già in uso.'],
            'password_confirm' => ['matches'   => 'Le password non coincidono.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');

        $users = new UserModel();
        $user  = new User([
            'username' => $username,
            'active'   => true,
            'nome'     => $this->request->getPost('nome'),
            'cognome'  => $this->request->getPost('cognome'),
            'ruolo'    => 'cliente',
        ]);

        $users->save($user);
        $user = $users->findById($users->getInsertID());

        $user->createEmailIdentity([
            'email'    => $username . '@portale.colombini-snc.it',
            'password' => $this->request->getPost('password'),
        ]);

        $user->addGroup('cliente');

        return redirect()->to('impostazioni/utenti')
            ->with('success', 'Utente portale "' . $username . '" creato con successo.');
    }

    public function utentiApp(): string
    {
        $users  = new UserModel();
        $utenti = $users->whereIn('ruolo', UserModel::RUOLI_APP)
                        ->orderBy('ruolo', 'ASC')->orderBy('cognome', 'ASC')
                        ->findAll();

        return view('impostazioni/utenti_app', [
            'title'      => 'Utenti App',
            'page_title' => 'Utenti App',
            'utenti'     => $utenti,
        ]);
    }

    public function creaUtenteApp(): string
    {
        return view('impostazioni/crea_utente_app', [
            'title'      => 'Nuovo Utente App',
            'page_title' => 'Nuovo Utente App',
        ]);
    }

    public function storeUtenteApp()
    {
        $ruoliValidi = implode(',', UserModel::RUOLI_APP);

        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'nome'             => 'required|max_length[100]',
            'cognome'          => 'required|max_length[100]',
            'ruolo'            => 'required|in_list[' . $ruoliValidi . ']',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'username'         => ['is_unique' => 'Questo nome utente è già in uso.'],
            'password_confirm' => ['matches'   => 'Le password non coincidono.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $ruolo    = $this->request->getPost('ruolo');

        $users = new UserModel();
        $user  = new User([
            'username' => $username,
            'active'   => true,
            'nome'     => $this->request->getPost('nome'),
            'cognome'  => $this->request->getPost('cognome'),
            'ruolo'    => $ruolo,
        ]);

        $users->save($user);
        $user = $users->findById($users->getInsertID());

        $user->createEmailIdentity([
            'email'    => $username . '@gestionale.colombini-snc.it',
            'password' => $this->request->getPost('password'),
        ]);

        $user->addGroup($ruolo);

        return redirect()->to('impostazioni/utenti-app')
            ->with('success', 'Utente "' . $username . '" creato con successo.');
    }

    public function editUtenteApp(int $id): string
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user || ! in_array($user->ruolo, UserModel::RUOLI_APP)) {
            return redirect()->to('impostazioni/utenti-app')->with('error', 'Utente non trovato.');
        }

        return view('impostazioni/edit_utente_app', [
            'title'      => 'Modifica Utente',
            'page_title' => 'Modifica Utente',
            'utente'     => $user,
        ]);
    }

    public function updateUtenteApp(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user || ! in_array($user->ruolo, UserModel::RUOLI_APP)) {
            return redirect()->to('impostazioni/utenti-app')->with('error', 'Utente non trovato.');
        }

        $rules = [
            'nome'    => 'required|max_length[100]',
            'cognome' => 'required|max_length[100]',
            'ruolo'   => 'required|in_list[' . implode(',', UserModel::RUOLI_APP) . ']',
        ];

        $password = $this->request->getPost('password');
        if ($password) {
            $rules['password']         = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (! $this->validate($rules, ['password_confirm' => ['matches' => 'Le password non coincidono.']])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nuovoRuolo = $this->request->getPost('ruolo');

        $users->update($id, [
            'nome'    => $this->request->getPost('nome'),
            'cognome' => $this->request->getPost('cognome'),
            'ruolo'   => $nuovoRuolo,
        ]);

        // aggiorna il gruppo Shield se il ruolo è cambiato
        if ($user->ruolo !== $nuovoRuolo) {
            $user->removeGroup($user->ruolo);
            $user->addGroup($nuovoRuolo);
        }

        if ($password) {
            $user->setPassword($password);
            $users->save($user);
        }

        return redirect()->to('impostazioni/utenti-app')
            ->with('success', 'Utente "' . $user->username . '" aggiornato.');
    }

    public function deleteUtenteApp(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user || ! in_array($user->ruolo, UserModel::RUOLI_APP)) {
            return redirect()->to('impostazioni/utenti-app')->with('error', 'Utente non trovato.');
        }

        $users->delete($id, true);

        return redirect()->to('impostazioni/utenti-app')
            ->with('success', 'Utente "' . $user->username . '" eliminato.');
    }

    public function deleteCliente(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user || $user->ruolo !== 'cliente') {
            return redirect()->to('impostazioni/utenti')->with('error', 'Utente non trovato.');
        }

        $users->delete($id, true);

        return redirect()->to('impostazioni/utenti')->with('success', 'Utente eliminato.');
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
        $force   = (bool) $this->request->getPost('force');
        $clienti = new ClienteModel();
        $geocoder = new GeocoderService();

        $query = $clienti->where('stato', 1)->where('geocoded_at IS NULL');
        if (! $force) {
            $query->where('geocodifica_fallita', 0);
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
            'done'       => false,
            'esito'      => $esito,
            'cliente'    => $nomeDisplay,
            'rimanenti'  => $rimanenti,
        ]);
    }
}
