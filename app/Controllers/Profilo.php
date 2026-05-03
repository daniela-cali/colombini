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
}
