<?php

namespace App\Models;

use CodeIgniter\Model;

class MagMovimentiModel extends Model
{
    protected $table         = 'mag_movimenti';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'tipo',
        'data',
        'num_documento',
        'fornitore_id',
        'cliente_id',
        'intervento_id',
        'note',
        'user_id',
    ];

    // Etichette leggibili per ogni tipo di movimento.
    public const TIPI = [
        'carico'      => 'Carico da fornitore',
        'scarico'     => 'Scarico manuale',
        'consegna'    => 'Consegna a cliente',
        'rettifica_p' => 'Rettifica (+)',
        'rettifica_m' => 'Rettifica (-)',
        'inventario'  => 'Giacenza iniziale',
    ];

    // I tipi che incrementano la giacenza.
    public const TIPI_CARICO = ['carico', 'rettifica_p', 'inventario'];

    // I tipi che decrementano la giacenza.
    public const TIPI_SCARICO = ['scarico', 'consegna', 'rettifica_m'];

    // Restituisce un movimento con le sue righe, il fornitore/cliente e il nome dell'utente.
    public function conRighe(int $id): ?array
    {
        $movimento = $this
            ->select('mag_movimenti.*, fornitori.ragione_sociale AS fornitore_nome, clienti.ragsoc AS cliente_nome, users.username AS utente_nome')
            ->join('fornitori', 'fornitori.id = mag_movimenti.fornitore_id', 'left')
            ->join('clienti',   'clienti.id = mag_movimenti.cliente_id',    'left')
            ->join('users',     'users.id = mag_movimenti.user_id',         'left')
            ->find($id);

        if (!$movimento) {
            return null;
        }

        $movimento['righe'] = $this->db->table('mag_movimenti_righe r')
            ->select('r.*, a.cod_articolo, a.descrizione, a.unita_misura')
            ->join('mag_articoli a', 'a.id = r.articolo_id', 'left')
            ->where('r.movimento_id', $id)
            ->get()->getResultArray();

        return $movimento;
    }

    // Restituisce lo storico movimenti di un singolo articolo, dal più recente.
    public function perArticolo(int $articoloId): array
    {
        return $this->db->table('mag_movimenti_righe r')
            ->select('mag_movimenti.*, r.quantita, r.costo_unitario, fornitori.ragione_sociale AS fornitore_nome, clienti.ragsoc AS cliente_nome')
            ->join('mag_movimenti', 'mag_movimenti.id = r.movimento_id',                  'inner')
            ->join('fornitori',     'fornitori.id = mag_movimenti.fornitore_id',           'left')
            ->join('clienti',       'clienti.id = mag_movimenti.cliente_id',               'left')
            ->where('r.articolo_id', $articoloId)
            ->orderBy('mag_movimenti.data', 'DESC')
            ->orderBy('mag_movimenti.id',   'DESC')
            ->get()->getResultArray();
    }

    // Restituisce i movimenti recenti con filtri opzionali per tipo e range di date.
    public function elenco(?string $tipo = null, ?string $dal = null, ?string $al = null): array
    {
        $builder = $this
            ->select('mag_movimenti.*, fornitori.ragione_sociale AS fornitore_nome, clienti.ragsoc AS cliente_nome, users.username AS utente_nome')
            ->join('fornitori', 'fornitori.id = mag_movimenti.fornitore_id', 'left')
            ->join('clienti',   'clienti.id = mag_movimenti.cliente_id',    'left')
            ->join('users',     'users.id = mag_movimenti.user_id',         'left')
            ->orderBy('mag_movimenti.data', 'DESC')
            ->orderBy('mag_movimenti.id',   'DESC');

        if ($tipo !== null) {
            $builder->where('mag_movimenti.tipo', $tipo);
        }
        if ($dal !== null) {
            $builder->where('mag_movimenti.data >=', $dal);
        }
        if ($al !== null) {
            $builder->where('mag_movimenti.data <=', $al);
        }

        return $builder->findAll();
    }
}
