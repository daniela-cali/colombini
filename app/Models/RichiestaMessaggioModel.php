<?php

namespace App\Models;

use CodeIgniter\Model;

class RichiestaMessaggioModel extends Model
{
    protected $table         = 'richieste_messaggi';
    protected $primaryKey    = 'id';
    protected $useTimestamps  = true;
    protected $updatedField   = '';
    protected $useSoftDeletes = false;
    protected $allowedFields  = ['richiesta_id', 'user_id', 'testo'];

    // Restituisce tutti i messaggi di una richiesta in ordine cronologico,
    // con nome e cognome dell'autore per la visualizzazione ad albero.
    public function perRichiesta(int $richiestaId): array
    {
        return $this->select('richieste_messaggi.*, u.nome, u.cognome')
                    ->join('users u', 'u.id = richieste_messaggi.user_id', 'left')
                    ->where('richiesta_id', $richiestaId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }
}
