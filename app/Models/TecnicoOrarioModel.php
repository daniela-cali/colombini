<?php

namespace App\Models;

use CodeIgniter\Model;

class TecnicoOrarioModel extends Model
{
    protected $table      = 'tecnici_orari';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = ['tecnico_id', 'giorno', 'ora_inizio', 'ora_fine', 'pausa_inizio', 'pausa_fine', 'attivo'];

    public const GIORNI = [
        1 => 'Lunedì',
        2 => 'Martedì',
        3 => 'Mercoledì',
        4 => 'Giovedì',
        5 => 'Venerdì',
        6 => 'Sabato',
        7 => 'Domenica',
    ];

    public function perTecnico(int $tecnicoId): array
    {
        $rows = $this->where('tecnico_id', $tecnicoId)->orderBy('giorno', 'ASC')->findAll();

        $indexed = [];
        foreach ($rows as $r) {
            $indexed[(int) $r['giorno']] = $r;
        }

        return $indexed;
    }

    public function salva(int $tecnicoId, array $post): void
    {
        foreach (self::GIORNI as $g => $label) {
            $attivo       = isset($post['attivo'][$g]) ? 1 : 0;
            $oraInizio    = $post['ora_inizio'][$g]    ?? '08:00';
            $oraFine      = $post['ora_fine'][$g]      ?? '17:00';
            $pausaInizio  = $post['pausa_inizio'][$g] ?? null;
            $pausaFine    = $post['pausa_fine'][$g]   ?? null;

            // se uno dei due è vuoto, azzera entrambi
            if (empty($pausaInizio) || empty($pausaFine)) {
                $pausaInizio = null;
                $pausaFine   = null;
            }

            $data = [
                'attivo'       => $attivo,
                'ora_inizio'   => $oraInizio,
                'ora_fine'     => $oraFine,
                'pausa_inizio' => $pausaInizio,
                'pausa_fine'   => $pausaFine,
            ];

            $existing = $this->where('tecnico_id', $tecnicoId)->where('giorno', $g)->first();

            if ($existing) {
                $this->update($existing['id'], $data);
            } else {
                $this->insert(array_merge($data, ['tecnico_id' => $tecnicoId, 'giorno' => $g]));
            }
        }
    }
}