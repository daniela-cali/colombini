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
        'bozza'       => ['label' => 'Bozza',      'badge' => 'badge-secondary'],
        'autorizzato' => ['label' => 'Approvato',  'badge' => 'badge-primary'],
        'in_corso'    => ['label' => 'In corso',   'badge' => 'badge-warning'],
        'completato'  => ['label' => 'Completato', 'badge' => 'badge-success'],
    ];

    public function perData(string $data, ?int $tecnicoId = null): array
    {
        $q = $this->select('viaggi.*, u.nome, u.cognome, u.colore,
                              v.nome AS veicolo_nome, v.targa AS veicolo_targa,
                              (SELECT COUNT(*) FROM viaggi_tappe vt WHERE vt.viaggio_id = viaggi.id) AS tappe_count')
                    ->join('users u', 'u.id = viaggi.tecnico_id')
                    ->join('veicoli v', 'v.id = viaggi.veicolo_id', 'left')
                    ->where('viaggi.data', $data)
                    ->orderBy('u.cognome');

        if ($tecnicoId) {
            $q->where('viaggi.tecnico_id', $tecnicoId);
        }

        return $q->findAll();
    }
    
    /* Recupera i viaggi di un'intera settimana (se tecnico loggato, solo i suoi) */
    public function perRange(string $dal, string $al, ?int $tecnicoId = null): array
    {
        $q = $this->select('viaggi.*, u.nome, u.cognome, u.colore,
                              v.nome AS veicolo_nome, v.targa AS veicolo_targa,
                              (SELECT COUNT(*) FROM viaggi_tappe vt WHERE vt.viaggio_id = viaggi.id) AS tappe_count')
                    ->join('users u', 'u.id = viaggi.tecnico_id')
                    ->join('veicoli v', 'v.id = viaggi.veicolo_id', 'left')
                    ->where('viaggi.data >=', $dal)
                    ->where('viaggi.data <=', $al)
                    ->orderBy('viaggi.data, u.cognome');

        if ($tecnicoId) {
            $q->where('viaggi.tecnico_id', $tecnicoId);
        }

        return $q->findAll();
    }

    public function conTappe(int $id): ?array
    {
        $viaggio = $this->select('viaggi.*, u.nome, u.cognome, u.colore, v.nome AS veicolo_nome, v.targa AS veicolo_targa')
                        ->join('users u', 'u.id = viaggi.tecnico_id')
                        ->join('veicoli v', 'v.id = viaggi.veicolo_id', 'left')
                        ->find($id);

        if (! $viaggio) return null;

        $viaggio['tappe'] = (new ViaggioTappaModel())->perViaggio($id);
        return $viaggio;
    }

    public function perId(int $idRicerca): ?array 
    {
        $viaggio = $this->select('viaggi.*, u.nome, u.cognome, u.colore,
                              v.nome AS veicolo_nome, v.targa AS veicolo_targa,
                              (SELECT COUNT(*) FROM viaggi_tappe vt WHERE vt.viaggio_id = viaggi.id) AS tappe_count')
                    ->join('users u', 'u.id = viaggi.tecnico_id')
                    ->join('veicoli v', 'v.id = viaggi.veicolo_id', 'left')
                    ->where('viaggi.id', $idRicerca)
                    ->orderBy('viaggi.data, u.cognome')
                    ->get()->getResultArray();

        if (! $viaggio) return [];
        return $viaggio;
    }
}
