<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table          = 'clienti';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'id_external',
        'codice',
        'tipo',
        'ragsoc',
        'nome',
        'cognome',
        'piva',
        'cfisc',
        'indirizzo',
        'citta',
        'cap',
        'provincia',
        'telefono',
        'email',
        'note',
        'contatti',
        'user_id',
        'dt_import',
        'stato',
    ];

    public function generaCodiceInterno(): string
    {
        $last = $this->like('codice', 'INT-', 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        $num = $last ? (int) substr($last['codice'], 4) + 1 : 1;
        return 'INT-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getNomeDisplay(array $cliente): string
    {
        if ($cliente['tipo'] === 'persona_fisica') {
            return trim(($cliente['cognome'] ?? '') . ' ' . ($cliente['nome'] ?? ''));
        }
        return $cliente['ragsoc'] ?? '';
    }

    public function ricerca(string $q): array
    {
        return $this->groupStart()
                        ->like('ragsoc', $q)
                        ->orLike('cognome', $q)
                        ->orLike('nome', $q)
                        ->orLike('codice', $q)
                        ->orLike('piva', $q)
                        ->orLike('cfisc', $q)
                    ->groupEnd()
                    ->where('stato', 1)
                    ->orderBy('ragsoc, cognome')
                    ->findAll();
    }
}