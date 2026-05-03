<?php

namespace App\Controllers;

class Report extends BaseController
{
    public function index(): string
    {
        return view('coming_soon', ['title' => 'Report', 'page_title' => 'Report']);
    }

    public function interventi(): string
    {
        return view('coming_soon', ['title' => 'Report Interventi', 'page_title' => 'Report Interventi']);
    }

    public function clienti(): string
    {
        return view('coming_soon', ['title' => 'Report Clienti', 'page_title' => 'Report Clienti']);
    }

    public function impianti(): string
    {
        return view('coming_soon', ['title' => 'Report Impianti', 'page_title' => 'Report Impianti']);
    }
}
