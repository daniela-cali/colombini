<?php

namespace App\Controllers;

class Impianti extends BaseController
{
    public function index(): string
    {
        return view('coming_soon', ['title' => 'Impianti', 'page_title' => 'Impianti']);
    }

    public function piscine(): string
    {
        return view('coming_soon', ['title' => 'Piscine', 'page_title' => 'Piscine']);
    }

    public function trattamento(): string
    {
        return view('coming_soon', ['title' => 'Trattamento Acqua', 'page_title' => 'Trattamento Acqua']);
    }

    public function create(): string
    {
        return view('coming_soon', ['title' => 'Nuovo Impianto', 'page_title' => 'Nuovo Impianto']);
    }

    public function store()
    {
        return redirect()->to('impianti');
    }

    public function show(int $id): string
    {
        return view('coming_soon', ['title' => 'Scheda Impianto', 'page_title' => 'Scheda Impianto']);
    }

    public function edit(int $id): string
    {
        return view('coming_soon', ['title' => 'Modifica Impianto', 'page_title' => 'Modifica Impianto']);
    }

    public function update(int $id)
    {
        return redirect()->to('impianti');
    }

    public function delete(int $id)
    {
        return redirect()->to('impianti');
    }
}
