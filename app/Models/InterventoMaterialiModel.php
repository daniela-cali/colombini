<?php

namespace App\Models;

use CodeIgniter\Model;

class InterventoMaterialiModel extends Model
{
    protected $table         = 'intervento_materiali';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'intervento_id',
        'articolo_id',
        'quantita',
        'movimento_id',
    ];

    // Restituisce le righe materiali di un intervento con codice e descrizione articolo.
    public function perIntervento(int $interventoId): array
    {
        return $this
            ->select('intervento_materiali.*, mag_articoli.cod_articolo, mag_articoli.descrizione')
            ->join('mag_articoli', 'mag_articoli.id = intervento_materiali.articolo_id')
            ->where('intervento_materiali.intervento_id', $interventoId)
            ->orderBy('intervento_materiali.id')
            ->findAll();
    }

    // Restituisce gli ID degli articoli più usati per un dato cliente, ordinati per frequenza.
    public function abitualiPerCliente(int $clienteId): array
    {
        $rows = $this
            ->select('intervento_materiali.articolo_id, COUNT(*) AS freq')
            ->join('interventi', 'interventi.id = intervento_materiali.intervento_id')
            ->where('interventi.cliente_id', $clienteId)
            ->groupBy('intervento_materiali.articolo_id')
            ->orderBy('freq', 'DESC')
            ->findAll();

        return array_column($rows, 'articolo_id');
    }
}
