<?php

namespace App\Models;

use CodeIgniter\Model;

class MagCategorieModel extends Model
{
    protected $table         = 'mag_categorie';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nome',
        'descrizione',
        'ordine',
        'attiva',
    ];

    // Restituisce id => nome per i select, solo categorie attive ordinate per campo ordine.
    public function comeLista(): array
    {
        $rows = $this->where('attiva', 1)
                     ->orderBy('ordine, nome')
                     ->findAll();

        $lista = [];
        foreach ($rows as $c) {
            $lista[$c['id']] = $c['nome'];
        }
        return $lista;
    }
}
