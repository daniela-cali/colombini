<?php

namespace App\Controllers;

class Preventivi extends BaseController
{
    public function index(): string
    {
        return view('coming_soon', ['title' => 'Preventivi', 'page_title' => 'Preventivi']);
    }

    public function create(): string
    {
        return view('coming_soon', ['title' => 'Nuovo Preventivo', 'page_title' => 'Nuovo Preventivo']);
    }

    public function store()
    {
        return redirect()->to('preventivi');
    }

    public function show(int $id): string
    {
        return view('coming_soon', ['title' => 'Dettaglio Preventivo', 'page_title' => 'Dettaglio Preventivo']);
    }

    public function edit(int $id): string
    {
        return view('coming_soon', ['title' => 'Modifica Preventivo', 'page_title' => 'Modifica Preventivo']);
    }

    public function update(int $id)
    {
        return redirect()->to('preventivi');
    }

    public function delete(int $id)
    {
        return redirect()->to('preventivi');
    }
}
