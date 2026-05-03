<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\InterventoModel;
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
            'durate'     => InterventoModel::DURATE,
            'tipi'       => InterventoModel::TIPI,
        ]);
    }

    public function salvaParametri()
    {
        $post = $this->request->getPost();

        // Sede aziendale
        foreach (['sede_nome', 'sede_indirizzo', 'sede_citta', 'sede_cap', 'sede_lat', 'sede_lng'] as $key) {
            setting()->set('Azienda.' . $key, $post[$key] ?? null);
        }

        // Orari default tecnici
        foreach (['orario_inizio', 'orario_fine', 'pausa_inizio', 'pausa_fine'] as $key) {
            setting()->set('Tecnici.' . $key, $post[$key] ?? null);
        }

        // Durate interventi
        foreach (array_keys(InterventoModel::DURATE) as $tipo) {
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
}
