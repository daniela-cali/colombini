<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\TipoInterventoModel;

class InterventoModel extends Model
{
    // TIPI e DURATE ora gestiti via DB → TipoInterventoModel::comeLista()
    protected $table          = 'interventi';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'richiesta_id',
        'tipo_intervento',
        'luogo_intervento',
        'citta',
        'lat',
        'lng',
        'geocoded_at',
        'cliente_id',
        'tecnico_id',
        'data_pianificata',
        'durata_viaggio',
        'durata_effettiva',
        'data_completamento',
        'descrizione',
        'note_interne',
        'stato',
    ];

    public static function getDurata(string $tipo): int
    {
        $val = setting('Interventi.durata_' . $tipo);
        if ($val !== null) {
            return (int) $val;
        }
        return TipoInterventoModel::durataDefault($tipo);
    }

    public const STATI = [
        'pianificato' => ['label' => 'Pianificato', 'badge' => 'badge-secondary'],
        'in_corso'    => ['label' => 'In corso',    'badge' => 'badge-warning'],
        'completato'  => ['label' => 'Completato',  'badge' => 'badge-success'],
        'annullato'   => ['label' => 'Annullato',   'badge' => 'badge-danger'],
    ];

    public function riepilogoPerTecnico(): array
    {
        $mese = date('n');
        $anno = date('Y');

        $rows = $this->db->query("
            SELECT
                u.id,
                u.nome,
                u.cognome,
                SUM(i.stato = 'pianificato')                                          AS pianificati,
                SUM(i.stato = 'in_corso')                                             AS in_corso,
                SUM(i.stato = 'completato')                                           AS totale_completati,
                SUM(i.stato = 'completato'
                    AND MONTH(i.data_completamento) = {$mese}
                    AND YEAR(i.data_completamento)  = {$anno})                        AS completati_mese,
                SUM(i.stato NOT IN ('annullato','completato'))                        AS aperti
            FROM users u
            LEFT JOIN interventi i ON i.tecnico_id = u.id AND i.deleted_at IS NULL
            WHERE u.ruolo = 'tecnico'
            GROUP BY u.id, u.nome, u.cognome
            ORDER BY u.cognome, u.nome
        ")->getResultArray();

        return $rows;
    }

    public function conDettagli(int $limit = 0): array
    {
        $q = $this->select('interventi.*,
                c.ragsoc AS cliente_ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome, c.deleted_at AS cliente_deleted_at,
                u_tec.nome AS tecnico_nome, u_tec.cognome AS tecnico_cognome, u_tec.colore AS tecnico_colore')
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
