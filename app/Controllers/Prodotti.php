<?php

namespace App\Controllers;

class Prodotti extends BaseController
{
    public function index(): string
    {
        return view('coming_soon', ['title' => 'Prodotti', 'page_title' => 'Prodotti']);
    }

    public function create(): string
    {
        return view('coming_soon', ['title' => 'Nuovo Prodotto', 'page_title' => 'Nuovo Prodotto']);
    }

    public function store()
    {
        return redirect()->to('prodotti');
    }

    public function show(int $id): string
    {
        return view('coming_soon', ['title' => 'Dettaglio Prodotto', 'page_title' => 'Dettaglio Prodotto']);
    }

    public function edit(int $id): string
    {
        return view('coming_soon', ['title' => 'Modifica Prodotto', 'page_title' => 'Modifica Prodotto']);
    }

    public function update(int $id)
    {
        return redirect()->to('prodotti');
    }

    public function delete(int $id)
    {
        return redirect()->to('prodotti');
    }
}
