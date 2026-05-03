<?php

namespace App\Controllers;

use App\Models\UserModel;
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
