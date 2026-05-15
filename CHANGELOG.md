# Changelog

Tutte le modifiche significative al progetto sono documentate in questo file.

Il formato segue [Keep a Changelog](https://keepachangelog.com/it/1.0.0/)
e il progetto adotta il [Versionamento Semantico](https://semver.org/lang/it/) `MAJOR.MINOR.PATCH`.

---

## [Non rilasciato]

---

## [0.5.0] — 2026-05-15

### Aggiunto
- **Colore tecnico** — campo `colore` sull'anagrafica utenti con color picker nelle form create/edit e pallino colorato nella lista e nella scheda
- **Sezione Sistema / Configurazione** nella sidebar: Tecnici, Tipi Intervento e Impostazioni raggruppati sotto un unico menu dropdown
- **Route `/sistema/tecnici`** — Tecnici spostati dalla sezione Anagrafiche alla sezione Sistema
- **Route `/sistema/tipi-intervento`** con CRUD completo; tabella DB `tipi_intervento` (codice, nome, durata default, attivo, ordine) precaricata con i 4 tipi esistenti
- **Elenco interventi raggruppato per tecnico** — una card per ogni tecnico con header colorato e interventi ordinati per data pianificata; sezione separata per i non assegnati
- **Geolocalizzazione interventi** — campi `citta`, `lat`, `lng`, `geocoded_at` sulla tabella `interventi`; pulsante Nominatim nei form create/edit; mini-mappa Leaflet nella scheda con fallback sulle coordinate del cliente
- **CHANGELOG.md** — file di versioning semantico (MAJOR.MINOR.PATCH)
- **Versione in sidebar** — letta dinamicamente da CHANGELOG.md e mostrata nell'header del menu

### Modificato
- `InterventoModel` — `TIPI` e `DURATE` rimossi come costanti PHP hardcoded; ora caricati dalla tabella `tipi_intervento` tramite `TipoInterventoModel::comeLista()`
- Impostazioni > Parametri — sezione durate interventi dinamica (legge i tipi dal DB)
- Elenco interventi — descrizione visibile in anteprima (80 car.) nella lista raggruppata
- `conDettagli()` — include `tecnico_colore` e (in show) `cliente_lat/lng` per la mappa di fallback

---

## [0.4.0] — 2026-05-03

### Aggiunto
- Dashboard dedicata per il tecnico con riepilogo interventi personali (pianificati, in corso, completati del mese)
- Riepilogo interventi per tecnico nella dashboard admin

### Modificato
- Sistemazione compatibilità classi Bootstrap 5 (`gap-2` → `mr-1` / `mb-1`)

---

## [0.3.0] — 2026-05-03

### Aggiunto
- Gestione utenti con ruoli: `admin`, `staff`, `tecnico`, `cliente`
- Pagina Impostazioni con parametri aziendali (sede, orari tecnici, durate standard interventi)
- Rilevamento coordinate sede tramite geocodifica (Nominatim / OpenStreetMap)
- Gestione orari di lavoro per tecnico (per giorno della settimana, con pausa pranzo)
- Favicon SVG personalizzata

---

## [0.2.0] — 2026-05-03

### Aggiunto
- Geocodifica automatica dei clienti (lat/lng da indirizzo)
- Import clienti da CSV con wizard (anteprima → mappa colonne → esecuzione)
- Minimappa Leaflet nella scheda cliente

### Corretto
- Eliminati flashmessage duplicati; i messaggi di successo spariscono dopo 4 secondi, gli alert restano fino alla chiusura manuale

---

## [0.1.0] — 2026-05-03

### Aggiunto
- Setup iniziale — Colombini Piscine gestionale
- Autenticazione con CodeIgniter Shield (login, logout, portale cliente)
- Gestione clienti (CRUD, scheda, stato attivo/inattivo)
- Gestione tecnici (CRUD, scheda, assegnazione interventi)
- Gestione interventi (CRUD, assegnazione tecnico, chiusura, stati: pianificato / in corso / completato / annullato)
- Gestione preventivi (CRUD)
- Portale cliente: nuova richiesta assistenza, storico richieste
- Dashboard admin con KPI, ultime richieste e ultimi interventi
- Impianti (stub — coming soon)
- Prodotti & Ricambi (stub — coming soon)
- Report / Statistiche (stub — coming soon)
