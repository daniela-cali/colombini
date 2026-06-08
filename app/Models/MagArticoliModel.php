<?php

namespace App\Models;

use CodeIgniter\Model;

class MagArticoliModel extends Model
{
    protected $table          = 'mag_articoli';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $beforeInsert = ['normalizza'];
    protected $beforeUpdate = ['normalizza'];

    protected $allowedFields  = [
        'cod_articolo',
        'descrizione',
        'unita_misura',
        'categoria_id',
        'fornitore_id',
        'posizione_id',
        'coordinate',
        'scorta_min',
        'lotto_riordino',
        'giacenza_corrente',
        'prezzo_acquisto',
        'perc_sconto_1',
        'perc_sconto_2',
        'perc_sconto_3',
        'prezzo_vendita',
        'note',
    ];

    // Normalizza i dati prima di insert/update: uppercase codice, cast int, FK vuote → null.
    protected function normalizza(array $data): array
    {
        $d = &$data['data'];
        if (isset($d['cod_articolo'])) {
            $d['cod_articolo'] = strtoupper(trim($d['cod_articolo']));
        }
        if (array_key_exists('unita_misura', $d) && empty($d['unita_misura'])) {
            $d['unita_misura'] = 'N';
        }
        foreach (['categoria_id', 'fornitore_id', 'posizione_id', 'coordinate', 'note',
                  'perc_sconto_1', 'perc_sconto_2', 'perc_sconto_3'] as $campo) {
            if (array_key_exists($campo, $d)) {
                $d[$campo] = $d[$campo] !== '' ? $d[$campo] : null;
            }
        }
        foreach (['scorta_min', 'lotto_riordino'] as $campo) {
            if (isset($d[$campo])) {
                $d[$campo] = (int) $d[$campo];
            }
        }
        return $data;
    }

    // Restituisce un articolo con i dati di categoria, fornitore e posizione già risolti.
    public function conDettagli(int $id): ?array
    {
        return $this
            ->select('mag_articoli.*, mag_categorie.nome AS categoria_nome, fornitori.ragione_sociale AS fornitore_nome, mag_posizioni.nome AS posizione_nome')
            ->join('mag_categorie', 'mag_categorie.id = mag_articoli.categoria_id', 'left')
            ->join('fornitori',     'fornitori.id = mag_articoli.fornitore_id',     'left')
            ->join('mag_posizioni', 'mag_posizioni.id = mag_articoli.posizione_id', 'left')
            ->find($id);
    }

    // Restituisce tutti gli articoli con dettagli JOIN, con filtro opzionale per categoria.
    public function elenco(?int $categoriaId = null): array
    {
        $builder = $this
            ->select('mag_articoli.*, mag_categorie.nome AS categoria_nome, fornitori.ragione_sociale AS fornitore_nome, mag_posizioni.nome AS posizione_nome')
            ->join('mag_categorie', 'mag_categorie.id = mag_articoli.categoria_id', 'left')
            ->join('fornitori',     'fornitori.id = mag_articoli.fornitore_id',     'left')
            ->join('mag_posizioni', 'mag_posizioni.id = mag_articoli.posizione_id', 'left')
            ->orderBy('mag_articoli.descrizione');

        if ($categoriaId !== null) {
            $builder->where('mag_articoli.categoria_id', $categoriaId);
        }

        return $builder->findAll();
    }

    // Restituisce gli articoli con giacenza sotto la scorta minima (scorta_min > 0).
    public function sottoScorta(): array
    {
        return $this
            ->select('mag_articoli.*, mag_categorie.nome AS categoria_nome, fornitori.ragione_sociale AS fornitore_nome')
            ->join('mag_categorie', 'mag_categorie.id = mag_articoli.categoria_id', 'left')
            ->join('fornitori',     'fornitori.id = mag_articoli.fornitore_id',     'left')
            ->where('mag_articoli.scorta_min > 0')
            ->where('mag_articoli.giacenza_corrente < mag_articoli.scorta_min')
            ->orderBy('mag_articoli.descrizione')
            ->findAll();
    }

    // Restituisce id => "[codice] descrizione" per i select nei form movimenti.
    public function comeLista(): array
    {
        $rows = $this->select('id, cod_articolo, descrizione')
                     ->orderBy('descrizione')
                     ->findAll();

        $lista = [];
        foreach ($rows as $r) {
            $lista[$r['id']] = '[' . $r['cod_articolo'] . '] ' . $r['descrizione'];
        }
        return $lista;
    }

    // Aggiorna la giacenza corrente atomicamente: delta positivo = carico, negativo = scarico.
    public function aggiornaGiacenza(int $articoloId, int $delta): bool
    {
        return $this->db->query(
            'UPDATE mag_articoli SET giacenza_corrente = giacenza_corrente + ? WHERE id = ?',
            [$delta, $articoloId]
        );
    }
}
