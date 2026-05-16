<?php

namespace App\Controllers;

class Profilo extends BaseController
{
    public function index(): string
    {
        return view('coming_soon', ['title' => 'Profilo', 'page_title' => 'Il Mio Profilo']);
    }

    public function update()
    {
        return redirect()->to('profilo');
    }

    public function changePassword(): string
    {
        return view('coming_soon', ['title' => 'Cambia Password', 'page_title' => 'Cambia Password']);
    }

    public function updatePassword()
    {
        return redirect()->to('profilo');
    }

    public function aggiornaVersioneVista()
    {
        $versione = $this->request->getPost('versione');
        if ($versione) {
            $user = auth()->user();
            (new \App\Models\UserModel())->update($user->id, ['ultima_versione_vista' => $versione]);
        }
        return $this->response->setJSON(['ok' => true]);
    }
}
