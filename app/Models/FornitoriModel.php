<?php

namespace App\Models;

use CodeIgniter\Model;

class FornitoriModel extends Model
{
    protected $table          = 'fornitori';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $beforeInsert = ['normalizza'];
    protected $beforeUpdate = ['normalizza'];

    protected $allowedFields  = [
        'ragione_sociale',
        'nome',
        'cognome',
        'referente',
        'telefono',
        'email',
        'indirizzo',
        'citta',
        'provincia',
        'cap',
        'piva',
        'note',
        'attivo',
    ];

    // Converte le stringhe vuote in null per tutti i campi facoltativi del fornitore.
    protected function normalizza(array $data): array
    {
        $d = &$data['data'];
        foreach (['ragione_sociale', 'nome', 'cognome', 'referente',
                  'telefono', 'email', 'indirizzo', 'citta',
                  'provincia', 'cap', 'piva', 'note'] as $campo) {
            if (array_key_exists($campo, $d)) {
                $d[$campo] = $d[$campo] !== '' ? $d[$campo] : null;
            }
        }
        return $data;
    }

    // Restituisce il nome da mostrare: ragione_sociale per ditte, cognome+nome per persone fisiche.
    public function getNomeDisplay(array $fornitore): string
    {
        if (!empty($fornitore['ragione_sociale'])) {
            return $fornitore['ragione_sociale'];
        }
        return trim(($fornitore['cognome'] ?? '') . ' ' . ($fornitore['nome'] ?? ''));
    }

    // Restituisce id => nome per i select e dropdown, solo fornitori attivi.
    public function comeLista(): array
    {
        $rows = $this->where('attivo', 1)
                     ->orderBy('ragione_sociale, cognome')
                     ->findAll();

        $lista = [];
        foreach ($rows as $f) {
            $lista[$f['id']] = $this->getNomeDisplay($f);
        }
        return $lista;
    }
}
