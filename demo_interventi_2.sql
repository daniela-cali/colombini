-- Inserimento via INSERT...SELECT: inserisce solo le righe dove il cliente esiste
INSERT INTO interventi
    (cliente_id, tipo_intervento, luogo_intervento, citta, lat, lng,
     priorita, stato, descrizione, created_at, updated_at)
SELECT c.id, v.tipo_intervento, v.luogo_intervento, v.citta, v.lat, v.lng,
       v.priorita, v.stato, v.descrizione, NOW(), NOW()
FROM (
    SELECT 'DEMO009' AS codice, 'addolcitori' AS tipo_intervento, 'Abitazione Ferraris'            AS luogo_intervento, 'Noli (SV)'                    AS citta, 44.202800 AS lat, 8.407100 AS lng, 'ordinario'   AS priorita, 'da_pianificare' AS stato, 'Rigenerazione resine addolcitore — consumo sale superiore alla norma segnalato dal cliente.'           AS descrizione UNION ALL
    SELECT 'DEMO010',           'piscine',       'Villaggio Turistico Riviera',     'Borgio Verezzi (SV)',         44.162300,   8.304200,   'urgente',     'da_pianificare', 'Pompa di calore piscina scoperta in avaria — stagione estiva in corso, intervento prioritario.' UNION ALL
    SELECT 'DEMO011',           'acquedotti',    'Condominio Le Palme',             'Ceriale (SV)',                44.093600,   8.233100,   'ordinario',   'da_pianificare', 'Verifica impianto centralizzato acqua potabile — analisi batteriologica scaduta.' UNION ALL
    SELECT 'DEMO012',           'filtri',        'Residence Aurora',                'Borghetto Santo Spirito (SV)',44.112800,   8.241700,   'programmato', 'da_pianificare', 'Sostituzione cartucce filtro UV e pulizia skimmer piscina condominiale — manutenzione annuale.' UNION ALL
    SELECT 'DEMO014',           'piscine',       'Hotel Corallo',                   'Diano Marina (IM)',           43.908200,   8.080500,   'ordinario',   'da_pianificare', 'Calibrazione dosatore automatico cloro — valori fuori range rilevati dal sistema di monitoraggio.' UNION ALL
    SELECT 'DEMO016',           'addolcitori',   'Abitazione Lanteri',              'Imperia (IM)',                43.884700,   8.028900,   'programmato', 'da_pianificare', 'Installazione nuovo addolcitore a doppio serbatoio — sostituzione impianto obsoleto.' UNION ALL
    SELECT 'DEMO018',           'piscine',       'Villetta Russi',                  'Alassio (SV)',                44.003100,   8.166700,   'urgente',     'da_pianificare', 'Perdita acqua vasca piscina interrata — calo livello 20 cm/giorno, urgente ispezione tenuta.' UNION ALL
    SELECT 'DEMO020',           'acquedotti',    'Agriturismo Cascina del Sole',    'Albenga (SV)',                44.049600,   8.220300,   'ordinario',   'da_pianificare', 'Manutenzione pozzo artesiano e impianto di pompaggio — verifica pressioni e portate.' UNION ALL
    SELECT 'DEMO022',           'filtri',        'Hotel Ariston',                   'Sanremo (IM)',                43.817200,   7.776600,   'programmato', 'da_pianificare', 'Revisione completa impianto filtrazione piscina coperta — inizio stagione invernale.' UNION ALL
    SELECT 'DEMO024',           'piscine',       'Camping Riviera dei Fiori',       'Ospedaletti (IM)',            43.799800,   7.728400,   'ordinario',   'da_pianificare', 'Controllo stagionale impianto piscina campo — verifica illuminazione subacquea e scala.' UNION ALL
    SELECT 'DEMO001',           'addolcitori',   'Hotel Riviera Palace',            'Alassio (SV)',                44.004800,   8.170900,   'programmato', 'da_pianificare', 'Verifica e ricarica sale addolcitore cucina — contratto manutenzione semestrale.' UNION ALL
    SELECT 'DEMO003',           'acquedotti',    'Camping Baia delle Sirene',       'Finale Ligure (SV)',          44.166800,   8.341200,   'urgente',     'da_pianificare', 'Contaminazione rete idrica campeggio — analisi chimica positiva a coliformi, intervento immediato.' UNION ALL
    SELECT 'DEMO007',           'piscine',       'Piscina Comunale',                'Pietra Ligure (SV)',          44.150300,   8.287400,   'ordinario',   'da_pianificare', 'Sostituzione proiettori subacquei a LED — adeguamento normativa impianti sportivi.' UNION ALL
    SELECT 'DEMO005',           'filtri',        'Hotel Savoy',                     'Loano (SV)',                  44.127400,   8.256800,   'programmato', 'da_pianificare', 'Pulizia e sanificazione impianto climatizzazione SPA — filtri HEPA da sostituire.' UNION ALL
    SELECT 'DEMO019',           'piscine',       'Country Club Albenga',            'Albenga (SV)',                44.053100,   8.209600,   'urgente',     'da_pianificare', 'Impianto riscaldamento vasca olimpionica fuori servizio — gara regionale in programma domenica.'
) v
JOIN clienti c ON c.codice = v.codice AND c.deleted_at IS NULL;
