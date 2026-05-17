<?php

namespace App\Models;

use CodeIgniter\Model;

class VeicoloModel extends Model
{
    protected $table      = 'veicoli';
    protected $primaryKey = 'id';

    protected $allowedFields = ['nome', 'targa', 'cambio_automatico', 'carico_massimo', 'attivo'];

    protected $useTimestamps = true;

    public function comeLista(): array
    {
        return $this->where('attivo', 1)
                    ->orderBy('nome')
                    ->findAll();
    }
}
