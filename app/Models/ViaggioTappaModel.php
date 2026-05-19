<?php

namespace App\Models;

use CodeIgniter\Model;

class ViaggioTappaModel extends Model
{
    protected $table         = 'viaggi_tappe';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'viaggio_id',
        'intervento_id',
        'ordine',
        'posizionato_manualmente',
        'distanza_da_precedente',
        'durata_da_precedente',
        'ora_arrivo_stimata',
    ];

    public function perViaggio(int $viaggioId): array
    {
        return $this->select('
                viaggi_tappe.*,
                i.tipo_intervento,
                i.luogo_intervento,
                i.citta,
                i.lat,
                i.lng,
                i.descrizione,
                i.stato AS intervento_stato,
                i.priorita,
                i.fissato,
                i.ora_inizio,
                i.durata_viaggio,
                ti.nome  AS tipo_nome,
                ti.icona AS tipo_icona,
                c.id   AS cliente_id,
                c.tipo AS cliente_tipo,
                c.ragsoc,
                c.cognome AS cliente_cognome,
                c.nome    AS cliente_nome,
                c.citta   AS cliente_citta
            ')
            ->join('interventi i',       'i.id = viaggi_tappe.intervento_id')
            ->join('tipi_intervento ti', 'ti.codice = i.tipo_intervento', 'left')
            ->join('clienti c',          'c.id = i.cliente_id', 'left')
            ->where('viaggi_tappe.viaggio_id', $viaggioId)
            ->orderBy('viaggi_tappe.ordine', 'ASC')
            ->findAll();
    }
}
