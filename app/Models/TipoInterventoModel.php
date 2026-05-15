<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoInterventoModel extends Model
{
    protected $table         = 'tipi_intervento';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['codice', 'nome', 'durata_default', 'attivo', 'ordine'];

    /** Restituisce ['piscine' => 'Piscine', ...] per i tipi attivi, ordinati */
    public static function comeLista(): array
    {
        $rows = (new self())->where('attivo', 1)->orderBy('ordine')->findAll();
        $out  = [];
        foreach ($rows as $row) {
            $out[$row['codice']] = $row['nome'];
        }
        return $out;
    }

    public static function durataDefault(string $codice): int
    {
        $row = (new self())->where('codice', $codice)->first();
        return $row ? (int) $row['durata_default'] : 60;
    }
}
