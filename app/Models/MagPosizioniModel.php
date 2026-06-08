<?php

namespace App\Models;

use CodeIgniter\Model;

class MagPosizioniModel extends Model
{
    protected $table         = 'mag_posizioni';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nome',
        'attiva',
    ];

    // Restituisce id => nome per i select, solo posizioni attive.
    public function comeLista(): array
    {
        $rows = $this->where('attiva', 1)
                     ->orderBy('nome')
                     ->findAll();

        $lista = [];
        foreach ($rows as $p) {
            $lista[$p['id']] = $p['nome'];
        }
        return $lista;
    }
}
