-- Inserimento via INSERT...SELECT: inserisce solo le righe dove il cliente esiste
INSERT INTO interventi
    (cliente_id, tipo_intervento, luogo_intervento, citta, lat, lng,
     priorita, stato, descrizione, created_at, updated_at)
SELECT c.id, v.tipo_intervento, v.luogo_intervento, v.citta, v.lat, v.lng,
       v.priorita, v.stato, v.descrizione, NOW(), NOW()
FROM (
    SELECT 'DEMO001' AS codice, 'piscine'     AS tipo_intervento, 'Hotel Riviera Palace'       AS luogo_intervento, 'Alassio (SV)'        AS citta, 44.004800 AS lat, 8.170900 AS lng, 'urgente'     AS priorita, 'da_pianificare' AS stato, 'Pompa di circolazione principale in avaria — piscina fuori servizio.'                            AS descrizione UNION ALL
    SELECT 'DEMO002',           'piscine',       'Residence Le Terrazze',          'Alassio (SV)',        44.007100,   8.175500,   'ordinario',   'da_pianificare', 'Manutenzione ordinaria semestrale impianto piscina condominiale.' UNION ALL
    SELECT 'DEMO003',           'filtri',        'Camping Baia delle Sirene',      'Finale Ligure (SV)',  44.166800,   8.341200,   'programmato', 'da_pianificare', 'Sostituzione filtri sabbia area acquascivoli — manutenzione programmata.' UNION ALL
    SELECT 'DEMO004',           'piscine',       'Abitazione Ferretti',            'Finale Ligure (SV)',  44.168500,   8.339700,   'ordinario',   'da_pianificare', 'Controllo parametri acqua piscina privata dopo riapertura stagionale.' UNION ALL
    SELECT 'DEMO005',           'acquedotti',    'Hotel Savoy',                    'Loano (SV)',          44.127400,   8.256800,   'urgente',     'da_pianificare', 'Perdita dal collettore principale — rischio danni strutturali, intervento urgente.' UNION ALL
    SELECT 'DEMO006',           'addolcitori',   'Abitazione Amoroso',             'Loano (SV)',          44.125800,   8.260100,   'ordinario',   'da_pianificare', 'Verifica impianto trattamento UV — unita fuori range, possibile sostituzione lampada.' UNION ALL
    SELECT 'DEMO007',           'piscine',       'Piscina Comunale',               'Pietra Ligure (SV)', 44.150300,   8.287400,   'programmato', 'da_pianificare', 'Revisione completa impianto di trattamento acqua — manutenzione annuale.' UNION ALL
    SELECT 'DEMO008',           'addolcitori',   'Abitazione Zunino',              'Pietra Ligure (SV)', 44.149100,   8.289000,   'ordinario',   'da_pianificare', 'Sostituzione membrane pompe dosatrici — rigenerazione resine addolcitore.' UNION ALL
    SELECT 'DEMO013',           'filtri',        'Camping La Pineta',              'Spotorno (SV)',       44.228600,   8.416800,   'urgente',     'da_pianificare', 'Filtro sabbia intasato — acqua torbida, reclami clienti campeggio.' UNION ALL
    SELECT 'DEMO015',           'addolcitori',   'Hotel Baia del Sole',            'Celle Ligure (SV)',   44.349200,   8.549700,   'ordinario',   'da_pianificare', 'Controllo e taratura centralina automatica dosaggio cloro e sale.' UNION ALL
    SELECT 'DEMO017',           'piscine',       'Hotel Bel Sit',                  'Andora (SV)',         43.953900,   8.136200,   'ordinario',   'da_pianificare', 'Controllo parametri piscina riscaldata e verifica impianto solare termico.' UNION ALL
    SELECT 'DEMO019',           'piscine',       'Country Club Albenga',           'Albenga (SV)',        44.053100,   8.209600,   'programmato', 'da_pianificare', 'Manutenzione pre-stagionale piscina esterna e jacuzzi.' UNION ALL
    SELECT 'DEMO021',           'piscine',       'Hotel Splendid',                 'Laigueglia (SV)',     43.977200,   8.160600,   'ordinario',   'da_pianificare', 'Manutenzione rooftop pool — verifica tenuta vasca e sistema di circolazione.' UNION ALL
    SELECT 'DEMO023',           'filtri',        'Residence Poggio Ligure',        'Vado Ligure (SV)',    44.266900,   8.437800,   'programmato', 'da_pianificare', 'Revisione semestrale filtri su due piscine del complesso — Vasca A e Vasca B.' UNION ALL
    SELECT 'DEMO025',           'piscine',       'Hotel Varazze Palace',           'Varazze (SV)',        44.361200,   8.574500,   'urgente',     'da_pianificare', 'Riscaldamento piscina panoramica non funzionante — ospiti senza servizio.'
) v
JOIN clienti c ON c.codice = v.codice AND c.deleted_at IS NULL;