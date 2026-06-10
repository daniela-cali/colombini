<?php

namespace App\Models;

use CodeIgniter\Model;

class InterventoMaterialiNoteModel extends Model
{
    const STATO_DA_PORTARE = 0;
    const STATO_FORNITO    = 1;

    protected $table         = 'intervento_materiali_note';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'cliente_id',
        'intervento_id',
        'articolo_id',
        'quantita',
        'stato',
        'note',
        'solo_note',
        'movimento_id',
    ];

    protected $beforeInsert = ['normalizza'];
    protected $beforeUpdate = ['normalizza'];

    // Imposta solo_note automaticamente: 1 quando non c'è articolo_id (riga di testo puro).
    protected function normalizza(array $data): array
    {
        if (array_key_exists('articolo_id', $data['data'])) {
            $data['data']['solo_note'] = empty($data['data']['articolo_id']) ? 1 : 0;
        }
        return $data;
    }

    // Tutte le righe del cliente (storico completo), ordinate per stato poi per id.
    public function perCliente(int $clienteId): array
    {
        return $this
            ->select('intervento_materiali_note.*, mag_articoli.cod_articolo, mag_articoli.descrizione')
            ->join('mag_articoli', 'mag_articoli.id = intervento_materiali_note.articolo_id', 'left')
            ->where('intervento_materiali_note.cliente_id', $clienteId)
            ->orderBy('intervento_materiali_note.stato', 'ASC')
            ->orderBy('intervento_materiali_note.id', 'ASC')
            ->findAll();
    }

    // Righe collegate a uno specifico intervento (tutti gli stati).
    public function perIntervento(int $interventoId): array
    {
        return $this
            ->select('intervento_materiali_note.*, mag_articoli.cod_articolo, mag_articoli.descrizione')
            ->join('mag_articoli', 'mag_articoli.id = intervento_materiali_note.articolo_id', 'left')
            ->where('intervento_materiali_note.intervento_id', $interventoId)
            ->orderBy('intervento_materiali_note.stato', 'ASC')
            ->orderBy('intervento_materiali_note.id', 'ASC')
            ->findAll();
    }

    // Solo righe stato=0 (da portare) per un intervento: pre-popola il modale di chiusura.
    public function daPortarePerIntervento(int $interventoId): array
    {
        return $this
            ->select('intervento_materiali_note.*, mag_articoli.cod_articolo, mag_articoli.descrizione')
            ->join('mag_articoli', 'mag_articoli.id = intervento_materiali_note.articolo_id', 'left')
            ->where('intervento_materiali_note.intervento_id', $interventoId)
            ->where('intervento_materiali_note.stato', self::STATO_DA_PORTARE)
            ->orderBy('intervento_materiali_note.id', 'ASC')
            ->findAll();
    }

    // Solo righe stato=1 (forniti) per un intervento: usato nel PDF e nella card di dettaglio.
    public function fornitiPerIntervento(int $interventoId): array
    {
        return $this
            ->select('intervento_materiali_note.*, mag_articoli.cod_articolo, mag_articoli.descrizione')
            ->join('mag_articoli', 'mag_articoli.id = intervento_materiali_note.articolo_id', 'left')
            ->where('intervento_materiali_note.intervento_id', $interventoId)
            ->where('intervento_materiali_note.stato', self::STATO_FORNITO)
            ->orderBy('intervento_materiali_note.id', 'ASC')
            ->findAll();
    }

    // Righe stato=0 del cliente non ancora collegate a nessun intervento: visibili nella scheda cliente.
    public function daPortarePerCliente(int $clienteId): array
    {
        return $this
            ->select('intervento_materiali_note.*, mag_articoli.cod_articolo, mag_articoli.descrizione')
            ->join('mag_articoli', 'mag_articoli.id = intervento_materiali_note.articolo_id', 'left')
            ->where('intervento_materiali_note.cliente_id', $clienteId)
            ->where('intervento_materiali_note.stato', self::STATO_DA_PORTARE)
            ->where('intervento_materiali_note.intervento_id IS NULL', null, false)
            ->orderBy('intervento_materiali_note.id', 'ASC')
            ->findAll();
    }
}
