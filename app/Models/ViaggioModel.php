<?php

namespace App\Models;

use CodeIgniter\Model;

class ViaggioModel extends Model
{
    protected $table         = 'viaggi';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'tecnico_id',
        'veicolo_id',
        'data',
        'stato',
        'distanza_totale',
        'durata_totale',
        'note',
    ];

    public const STATI = [
        'bozza'      => ['label' => 'Bozza',      'badge' => 'badge-secondary'],
        'autorizzato' => ['label' => 'Autorizzato', 'badge' => 'badge-primary'],
        'in_corso'   => ['label' => 'In corso',   'badge' => 'badge-warning'],
        'completato' => ['label' => 'Completato', 'badge' => 'badge-success'],
    ];

    public function perData(string $data): array
    {
        return $this->select('viaggi.*, u.nome, u.cognome, u.colore, v.nome AS veicolo_nome')
                    ->join('users u', 'u.id = viaggi.tecnico_id')
                    ->join('veicoli v', 'v.id = viaggi.veicolo_id', 'left')
                    ->where('viaggi.data', $data)
                    ->orderBy('u.cognome')
                    ->findAll();
    }

    public function conTappe(int $id): ?array
    {
        $viaggio = $this->select('viaggi.*, u.nome, u.cognome, u.colore, v.nome AS veicolo_nome')
                        ->join('users u', 'u.id = viaggi.tecnico_id')
                        ->join('veicoli v', 'v.id = viaggi.veicolo_id', 'left')
                        ->find($id);

        if (! $viaggio) return null;

        $viaggio['tappe'] = (new ViaggioTappaModel())->perViaggio($id);
        return $viaggio;
    }
}
