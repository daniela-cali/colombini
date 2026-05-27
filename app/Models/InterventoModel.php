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
        'note_chiusura',
        'firma_cliente',
        'firma_at',
        'firma_tecnico',
        'firma_tecnico_at',
        'stato',
        'priorita',
        'fissato',
        'ora_inizio',
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
        'da_pianificare' => ['label' => 'Da pianificare', 'badge' => 'badge-info'],
        'pianificato'    => ['label' => 'Pianificato',    'badge' => 'badge-secondary'],
        'in_corso'       => ['label' => 'In corso',       'badge' => 'badge-warning'],
        'completato'     => ['label' => 'Completato',     'badge' => 'badge-success'],
        'annullato'      => ['label' => 'Annullato',      'badge' => 'badge-danger'],
    ];

    public const PRIORITA = [
        'urgente'     => ['label' => 'Urgente',     'badge' => 'badge-danger'],
        'ordinario'   => ['label' => 'Ordinario',   'badge' => 'badge-secondary'],
        'programmato' => ['label' => 'Programmato', 'badge' => 'badge-info'],
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
                u.colore,
                SUM(i.stato = 'pianificato')                                          AS pianificati,
                SUM(i.stato = 'in_corso')                                             AS in_corso,
                SUM(i.stato = 'completato')                                           AS totale_completati,
                SUM(i.stato = 'completato'
                    AND MONTH(i.data_completamento) = {$mese}
                    AND YEAR(i.data_completamento)  = {$anno})                        AS completati_mese,
                SUM(i.stato NOT IN ('annullato','completato'))                        AS aperti
            FROM users u
            LEFT JOIN interventi i ON i.tecnico_id = u.id AND i.deleted_at IS NULL
            WHERE (u.ruolo = 'tecnico' OR u.assegnabile_interventi = 1)
            GROUP BY u.id, u.nome, u.cognome
            ORDER BY u.cognome, u.nome
        ")->getResultArray();

        return $rows;
    }

    public function conDettagli(int $limit = 0, ?int $tecnicoId = null, ?string $statoFiltro = null, bool $escludiCompletati = false, ?int $clienteId = null): array
    {
        $q = $this->select('interventi.*,
                c.ragsoc AS cliente_ragsoc, c.nome AS cliente_nome, c.cognome AS cliente_cognome, c.deleted_at AS cliente_deleted_at,
                u_tec.nome AS tecnico_nome, u_tec.cognome AS tecnico_cognome, u_tec.colore AS tecnico_colore, u_tec.id AS tecnico_id')
            ->join('clienti c', 'c.id = interventi.cliente_id', 'left')
            ->join('users u_tec', 'u_tec.id = interventi.tecnico_id', 'left')
            ->orderBy('interventi.created_at', 'DESC');

        if ($tecnicoId) {
            $q->where('interventi.tecnico_id', $tecnicoId);
        }

        if ($clienteId) {
            $q->where('interventi.cliente_id', $clienteId);
        }

        if ($statoFiltro) {
            $q->where('interventi.stato', $statoFiltro);
        } elseif ($escludiCompletati) {
            $q->whereNotIn('interventi.stato', ['completato', 'annullato']);
        }

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

    // Chi ha completato più interventi di questo tipo per quel cliente (fallback: qualsiasi cliente).
    // Usato come segnale "storico" per suggerire esperienza specifica.
    public function tecnicoConsigliato(string $tipo, int $clienteId = 0): ?array
    {
        $base = fn() => db_connect()->table('interventi i')
            ->select('i.tecnico_id, u.nome, u.cognome, COUNT(*) AS cnt')
            ->join('users u', 'u.id = i.tecnico_id')
            ->where('i.stato', 'completato')
            ->where('i.tecnico_id IS NOT NULL', null, false)
            ->where('i.deleted_at IS NULL', null, false)
            ->where('i.tipo_intervento', $tipo)
            ->groupBy('i.tecnico_id, u.nome, u.cognome')
            ->orderBy('cnt', 'DESC')
            ->limit(1);

        if ($clienteId) {
            $row = $base()->where('i.cliente_id', $clienteId)->get()->getRowArray();
            if ($row) return $row;
        }

        return $base()->get()->getRowArray() ?: null;
    }

    // Referente (livello 3) del tipo dato meno occupato nel giorno indicato,
    // escluso se supera $soglia interventi già pianificati quel giorno.
    public function tecnicoReferente(string $tipo, string $data, int $soglia = 4): ?array
    {
        $db = db_connect();

        $tipoRow = $db->table('tipi_intervento')
            ->select('id')
            ->where('codice', $tipo)
            ->get()->getRowArray();

        if (!$tipoRow) return null;

        $tipoId = (int) $tipoRow['id'];

        $row = $db->table('users u')
            ->select('u.id AS tecnico_id, u.nome, u.cognome, COUNT(i.id) AS cnt')
            ->join(
                'tecnici_competenze tc',
                'tc.tecnico_id = u.id AND tc.tipo_intervento_id = ' . $tipoId . ' AND tc.livello = 3',
                'inner'
            )
            ->join(
                'interventi i',
                "i.tecnico_id = u.id AND i.stato IN ('pianificato', 'in_corso') AND DATE(i.data_pianificata) = " . $db->escape($data),
                'left'
            )
            ->where('u.deleted_at IS NULL', null, false)
            ->where('u.active', 1)
            ->groupBy('u.id, u.nome, u.cognome')
            ->having('COUNT(i.id) <', $soglia)
            ->orderBy('cnt', 'ASC')
            ->orderBy('u.cognome', 'ASC')
            ->limit(1)
            ->get()->getRowArray();

        return $row ?: null;
    }

    // Tecnico con competenza >= 2 sul tipo dato che ha il minor numero di interventi
    // pianificati/in corso nel giorno indicato. Fallback quando mancano referente e storico.
    public function tecnicoMenoOccupato(string $tipo, string $data): ?array
    {
        $db = db_connect();

        $tipoRow = $db->table('tipi_intervento')
            ->select('id')
            ->where('codice', $tipo)
            ->get()->getRowArray();

        if (!$tipoRow) return null;

        $tipoId = (int) $tipoRow['id'];

        return $db->table('users u')
            ->select('u.id AS tecnico_id, u.nome, u.cognome, COUNT(i.id) AS cnt')
            ->join(
                'tecnici_competenze tc',
                'tc.tecnico_id = u.id AND tc.tipo_intervento_id = ' . $tipoId . ' AND tc.livello >= 2',
                'inner'
            )
            ->join(
                'interventi i',
                "i.tecnico_id = u.id AND i.stato IN ('pianificato', 'in_corso') AND DATE(i.data_pianificata) = " . $db->escape($data),
                'left'
            )
            ->where('u.deleted_at IS NULL', null, false)
            ->where('u.active', 1)
            ->groupBy('u.id, u.nome, u.cognome')
            ->orderBy('cnt', 'ASC')
            ->orderBy('u.cognome', 'ASC')
            ->limit(1)
            ->get()->getRowArray() ?: null;
    }
}
