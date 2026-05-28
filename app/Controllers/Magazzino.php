<?php

namespace App\Controllers;

class Magazzino extends BaseController
{
    // Placeholder per le sezioni magazzino non ancora implementate;
    // $tipo = piscine|trattamento, $categoria = impianti|ricambi.
    public function lista(string $tipo, string $categoria = ''): string
    {
        $titoli = [
            'piscine'      => 'Piscine',
            'trattamento'  => 'Trattamento Acqua',
        ];
        $label = ($titoli[$tipo] ?? ucfirst($tipo))
               . ($categoria ? ' — ' . ucfirst($categoria) : '');

        return view('coming_soon', [
            'title'      => 'Magazzino',
            'page_title' => 'Magazzino · ' . $label,
        ]);
    }
}
