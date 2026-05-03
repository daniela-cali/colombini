<?php

namespace App\Models;

use CodeIgniter\Model;

class RichiestaModel extends Model
{
    protected $table         = 'richieste_assistenza';
    protected $primaryKey    = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'user_id',
        'tipo_intervento',
        'luogo_intervento',
        'richiedente',
        'telefono_contatto',
        'note',
        'stato',
    ];

    public const TIPI = [
        'piscine'      => 'Piscine',
        'addolcitori'  => 'Addolcitori',
        'acquedotti'   => 'Acquedotti',
        'commerciale'  => 'Richiesta Commerciale',
    ];

    public const STATI = [
        'nuova'         => ['label' => 'Nuova',          'badge' => 'badge-info'],
        'in_lavorazione'=> ['label' => 'In lavorazione', 'badge' => 'badge-warning'],
        'chiusa'        => ['label' => 'Chiusa',         'badge' => 'badge-success'],
    ];

    public function perCliente(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function ultime(int $limit = 5): array
    {
        return $this->select('richieste_assistenza.*, u.username, c.ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome, c.tipo AS cliente_tipo')
                    ->join('users u', 'u.id = richieste_assistenza.user_id', 'left')
                    ->join('clienti c', 'c.user_id = richieste_assistenza.user_id', 'left')
                    ->orderBy('richieste_assistenza.created_at', 'DESC')
                    ->findAll($limit);
    }
}
