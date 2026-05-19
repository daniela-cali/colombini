<?php

namespace App\Models;

use CodeIgniter\Model;

class TecnicoCompetenzaModel extends Model
{
    protected $table      = 'tecnici_competenze';
    protected $primaryKey = 'id';

    protected $allowedFields = ['tecnico_id', 'tipo_intervento_id', 'livello'];

    protected $useTimestamps = false;

    const LIVELLI = [
        0 => 'Non competente',
        1 => 'Base',
        2 => 'Autonomo',
        3 => 'Referente',
    ];

    public function perTecnico(int $tecnicoId): array
    {
        return $this->where('tecnico_id', $tecnicoId)
                    ->findAll();
    }

    public function salva(int $tecnicoId, array $post): void
    {
        $tipi   = $post['tipo_intervento_id'] ?? [];
        $livelli = $post['livello'] ?? [];

        $this->where('tecnico_id', $tecnicoId)->delete();

        $batch = [];
        foreach ($tipi as $i => $tipoId) {
            $livelloRaw = $livelli[$i] ?? '';
            if ($livelloRaw === '' || $livelloRaw === null) continue;
            $livello = (int) $livelloRaw;
            if ($tipoId && in_array($livello, [0, 1, 2, 3])) {
                $batch[] = [
                    'tecnico_id'         => $tecnicoId,
                    'tipo_intervento_id' => (int) $tipoId,
                    'livello'            => $livello,
                ];
            }
        }

        if (! empty($batch)) {
            $this->insertBatch($batch);
        }
    }
}
