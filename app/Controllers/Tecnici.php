<?php

namespace App\Controllers;

use App\Models\TipoInterventoModel;
use App\Models\InterventoModel;
use App\Models\TecnicoOrarioModel;
use App\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class Tecnici extends BaseController
{
    public function index(): string
    {
        $users   = new UserModel();
        $tecnici = $users->where('ruolo', 'tecnico')->orderBy('cognome', 'ASC')->findAll();

        return view('tecnici/index', [
            'title'      => 'Tecnici',
            'page_title' => 'Tecnici',
            'tecnici'    => $tecnici,
        ]);
    }

    public function create(): string
    {
        return view('tecnici/create', [
            'title'      => 'Nuovo Tecnico',
            'page_title' => 'Nuovo Tecnico',
        ]);
    }

    public function store()
    {
        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'nome'             => 'required|max_length[100]',
            'cognome'          => 'required|max_length[100]',
            'telefono'         => 'permit_empty|max_length[30]',
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
            'telefono' => $this->request->getPost('telefono'),
            'colore'   => $this->request->getPost('colore') ?: null,
            'ruolo'    => 'tecnico',
        ]);

        $users->save($user);
        $user = $users->findById($users->getInsertID());

        $user->createEmailIdentity([
            'email'    => $username . '@gestionale.colombini-snc.it',
            'password' => $this->request->getPost('password'),
        ]);

        $user->addGroup('tecnico');

        return redirect()->to('sistema/tecnici')
            ->with('success', 'Tecnico "' . $username . '" creato con successo.');
    }

    public function show(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || $tecnico->ruolo !== 'tecnico') {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $interventi = new InterventoModel();
        $orariModel = new TecnicoOrarioModel();

        return view('tecnici/show', [
            'title'          => 'Scheda Tecnico',
            'page_title'     => 'Scheda Tecnico',
            'tecnico'        => $tecnico,
            'interventi'     => $interventi->perTecnico($id),
            'tipi'           => TipoInterventoModel::comeLista(),
            'stati'          => InterventoModel::STATI,
            'orari'          => $orariModel->perTecnico($id),
            'giorni'         => TecnicoOrarioModel::GIORNI,
            'orariDefault'   => [
                'ora_inizio'   => setting('Tecnici.orario_inizio')   ?? '08:00',
                'ora_fine'     => setting('Tecnici.orario_fine')     ?? '17:00',
                'pausa_inizio' => setting('Tecnici.pausa_inizio')    ?? '13:00',
                'pausa_fine'   => setting('Tecnici.pausa_fine')      ?? '14:00',
            ],
        ]);
    }

    public function edit(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || $tecnico->ruolo !== 'tecnico') {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        return view('tecnici/edit', [
            'title'      => 'Modifica Tecnico',
            'page_title' => 'Modifica Tecnico',
            'tecnico'    => $tecnico,
        ]);
    }

    public function update(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || $tecnico->ruolo !== 'tecnico') {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $rules = [
            'nome'    => 'required|max_length[100]',
            'cognome' => 'required|max_length[100]',
            'telefono'=> 'permit_empty|max_length[30]',
            'ruolo'   => 'required|in_list[' . implode(',', UserModel::RUOLI_APP) . ']',
        ];

        $password = $this->request->getPost('password');
        if ($password) {
            $rules['password']         = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        $messages = [
            'password_confirm' => ['matches' => 'Le password non coincidono.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nuovoRuolo = $this->request->getPost('ruolo');

        $users->update($id, [
            'nome'     => $this->request->getPost('nome'),
            'cognome'  => $this->request->getPost('cognome'),
            'telefono' => $this->request->getPost('telefono') ?: null,
            'colore'   => $this->request->getPost('colore') ?: null,
            'ruolo'    => $nuovoRuolo,
        ]);

        if ($tecnico->ruolo !== $nuovoRuolo) {
            $tecnico->removeGroup($tecnico->ruolo);
            $tecnico->addGroup($nuovoRuolo);
        }

        if ($password) {
            $tecnico->setPassword($password);
            $users->save($tecnico);
        }

        return redirect()->to('sistema/tecnici/' . $id)->with('success', 'Tecnico aggiornato.');
    }

    public function orariUpdate(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || $tecnico->ruolo !== 'tecnico') {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $orariModel = new TecnicoOrarioModel();
        $orariModel->salva($id, $this->request->getPost());

        return redirect()->to('sistema/tecnici/' . $id)->with('success', 'Orari di lavoro aggiornati.');
    }

    public function delete(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || $tecnico->ruolo !== 'tecnico') {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $users->delete($id, true);

        return redirect()->to('sistema/tecnici')->with('success', 'Tecnico eliminato.');
    }
}
