<?php

namespace App\Models;

use CodeIgniter\Model;

class MagMovimentiRigheModel extends Model
{
    protected $table         = 'mag_movimenti_righe';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'movimento_id',
        'articolo_id',
        'quantita',
        'costo_unitario',
    ];

    // Restituisce le righe di un movimento con descrizione e codice articolo.
    public function perMovimento(int $movimentoId): array
    {
        return $this->db->table('mag_movimenti_righe r')
            ->select('r.*, a.cod_articolo, a.descrizione, a.unita_misura')
            ->join('mag_articoli a', 'a.id = r.articolo_id', 'left')
            ->where('r.movimento_id', $movimentoId)
            ->orderBy('r.id')
            ->get()->getResultArray();
    }
}
