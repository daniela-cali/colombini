<?php

namespace App\Models;

use CodeIgniter\Model;

class InterventoModel extends Model
{
    protected $table          = 'interventi';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'richiesta_id',
        'tipo_intervento',
        'luogo_intervento',
        'cliente_id',
        'tecnico_id',
        'data_pianificata',
        'data_completamento',
        'descrizione',
        'note_interne',
        'stato',
    ];

    public const TIPI = [
        'piscine'     => 'Piscine',
        'addolcitori' => 'Addolcitori',
        'acquedotti'  => 'Acquedotti',
        'commerciale' => 'Richiesta Commerciale',
    ];

    public const STATI = [
        'pianificato' => ['label' => 'Pianificato', 'badge' => 'badge-secondary'],
        'in_corso'    => ['label' => 'In corso',    'badge' => 'badge-warning'],
        'completato'  => ['label' => 'Completato',  'badge' => 'badge-success'],
        'annullato'   => ['label' => 'Annullato',   'badge' => 'badge-danger'],
    ];

    public function conDettagli(int $limit = 0): array
    {
        $q = $this->select('interventi.*,
                c.ragsoc AS cliente_ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome,
                u_tec.nome AS tecnico_nome, u_tec.cognome AS tecnico_cognome')
            ->join('clienti c', 'c.id = interventi.cliente_id', 'left')
            ->join('users u_tec', 'u_tec.id = interventi.tecnico_id', 'left')
            ->orderBy('interventi.created_at', 'DESC');

        return $limit ? $q->findAll($limit) : $q->findAll();
    }

    public function perTecnico(int $tecnicoId): array
    {
        return $this->select('interventi.*,
                c.ragsoc AS cliente_ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome')
            ->join('clienti c', 'c.id = interventi.cliente_id', 'left')
            ->where('interventi.tecnico_id', $tecnicoId)
            ->orderBy('interventi.data_pianificata', 'ASC')
            ->findAll();
    }
}
