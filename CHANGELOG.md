# Changelog

Tutte le modifiche significative al progetto sono documentate in questo file.

Il formato segue [Keep a Changelog](https://keepachangelog.com/it/1.0.0/)
e il progetto adotta il [Versionamento Semantico](https://semver.org/lang/it/) `MAJOR.MINOR.PATCH`.

---

## [Non rilasciato]

---

## [0.8.8] — 2026-05-16

### Aggiunto
- **Note di chiusura** — modal al click di "Chiudi" con textarea opzionale; colonna `note_chiusura TEXT` sulla tabella `interventi`; visibile nella scheda se valorizzata
- **Richiedente** nella scheda intervento — mostrato solo se presente e diverso dal nome cliente (dati dalla richiesta portale collegata)

### Corretto
- Fix accessibilità Bootstrap 4: `aria-hidden` impostato prima che il focus uscisse dal modal; risolto con blur globale su `hide.bs.modal`
- Modal "Chiudi" non appariva: era posizionato fuori da `$this->section('content')` e veniva scartato da CI4

---

## [0.8.7] — 2026-05-16

### Aggiunto
- **Drag & drop sul calendario** — trascinamento degli eventi per aggiornare `data_pianificata`; endpoint `POST /calendario/sposta`; revert automatico in caso di errore
- **Route calendario raggruppate** in `$routes->group('calendario', ...)`
- **Timezone applicazione** impostata su `Europe/Rome` in `App.php`

### Corretto
- Tooltip Bootstrap rimasto nel DOM dopo il drag: rimosso via `eventDragStart` / `eventDragStop` con classe `fc-dragging` su body
- `longPressDelay: 300` per drag su touch più reattivo
- CSRF rigenerato correttamente dopo ogni POST: il nuovo hash viene restituito nella risposta JSON e aggiornato in JS
- Datetime inviato al server in ora locale (non UTC) per evitare conversioni errate

---

## [0.8.6] — 2026-05-16

### Modificato
- **Icone tipo intervento** visibili in lista interventi, dashboard admin (ultimi interventi e ultime richieste portale) e dashboard tecnico
- **Correzione** colonna "Tipo" nelle ultime richieste portale: mostrava il codice grezzo invece del nome (usava `$stati_richiesta` anziché `$tipi`)

---

## [0.8.5] — 2026-05-16

### Aggiunto
- **Modal "Novità"** — al primo accesso dopo un aggiornamento mostra le novità della versione corrente; si chiude solo dopo conferma AJAX che aggiorna `ultima_versione_vista` sul profilo utente
- **Versione in navbar** — badge cliccabile a sinistra del nome utente che apre il changelog completo
- **Ruolo `operativo`** aggiunto alle costanti `RUOLI` e `RUOLI_APP` del `UserModel`
- **Migration** `AddUltimaVersioneVista` — colonna `ultima_versione_vista VARCHAR(20)` sulla tabella `users`
- **Endpoint** `POST /profilo/versione-vista` — aggiorna la versione vista dall'utente corrente

### Modificato
- Versione rimossa dal brand della sidebar (spostata in navbar)

---

## [0.8.4] — 2026-05-16

### Aggiunto
- **Modal al click** sul evento del calendario: riepilogo cliente, tecnico, tipo, data, stato e descrizione; bottoni Apri scheda e Modifica
- **`?from=calendario`** nei link del modal: breadcrumb e pulsante ✕ funzionano anche dalla pagina modifica intervento
- **Redirect post-salvataggio** al calendario se l'edit è stato aperto dal calendario (campo hidden `from`)
- **Locale italiana** FullCalendar: script `@fullcalendar/core/locales/it.global.min.js`

### Corretto
- Rimosso CSS link FullCalendar inesistente (gli stili sono iniettati dal bundle JS)

---

## [0.8.2] — 2026-05-15

### Corretto
- **Eliminazione tecnico** bloccata se ha interventi aperti (pianificati o in corso); messaggio d'errore con conteggio e redirect alla scheda
- **Eliminazione tipo intervento** bloccata se usato da almeno un intervento; suggerisce di disattivarlo invece
- Guide in linea aggiornate per riflettere il comportamento reale dell'eliminazione

---

## [0.8.1] — 2026-05-15

### Aggiunto
- **Guida in linea** su tutte le pagine gestionali (18 view): calendario, clienti, interventi, tecnici, tipi intervento, dashboard, impostazioni

---

## [0.8.0] — 2026-05-15

### Aggiunto
- **Calendario interventi** — pagina `/calendario` con FullCalendar v6: vista giorno/settimana/mese, eventi colorati per tecnico, icona del tipo intervento accanto all'orario, testo cliente sempre visibile
- **Endpoint JSON** `/calendario/eventi` — restituisce gli interventi nel range di date richiesto da FullCalendar
- **Calendario in sidebar** — voce nel Cruscotto accanto alla Dashboard
- **Ritorno contestuale** dalla scheda intervento: se aperta dal calendario, breadcrumb e pulsante ✕ riportano al calendario

### Modificato
- **Palette colori tecnici** — sostituiti i 12 colori vivaci con equivalenti pastello (più leggibili come sfondo negli eventi del calendario)
- **Testo eventi calendario** — `eventTextColor: #1f2937` per contrasto su sfondi pastello
- **Checkmark palette tecnici** — colore del segno di spunta cambiato da bianco a `#222`

---

## [0.7.1] — 2026-05-15

### Aggiunto
- `app/Language/it/Auth.php` — traduzione italiana completa dei messaggi di Shield (login, password, token, 2FA, attivazione, gruppi)

---

## [0.7.0] — 2026-05-15

### Aggiunto
- **Colonna `icona` su `tipi_intervento`** — migrazione con pre-popolamento dei 4 tipi esistenti
- **Icon picker** nel gestione tipi intervento (create/edit): pannello con ~60 icone FA5 ricercabili per nome o parola chiave italiana, anteprima live
- **Colonna icona visibile** nell'elenco tipi intervento
- **Portale cliente** — tipi intervento letti da DB (`TipoInterventoModel`) invece di `RichiestaModel::TIPI`; icone dinamiche con fallback `fa-tools`
- `TipoInterventoModel::comeListaCompleta()` — nuovo metodo che restituisce i record completi

### Corretto
- `Sistema::tipiInterventoStore/Update` — campo `icona` ora incluso nel salvataggio

---

## [0.6.2] — 2026-05-15

### Modificato
- Header card elenco clienti: rimosso `card-outline` per avere sfondo blu pieno con titolo bianco ad alto contrasto; bottoni aggiornati a `btn-outline-light` / `btn-light text-dark`

### Corretto
- Icona occhio duplicata sui campi password: il layout `admin.php` ora salta l'aggiunta automatica del toggle se esiste già un `input-group-append`; rimossi i toggle manuali ridondanti da `tecnici/create`, `impostazioni/crea_cliente`, `clienti/crea_portale`

---

## [0.6.1] — 2026-05-15

### Corretto
- Campi password nella form modifica tecnico: aggiunto `autocomplete="new-password"` per impedire ai browser di riempire automaticamente le credenziali salvate

---

## [0.6.0] — 2026-05-15

### Aggiunto
- **Color picker a palette** — nelle form crea/modifica tecnico il native color picker è sostituito da 12 cerchi colorati cliccabili con checkmark sul selezionato (blu, rosso, verde, ambra, viola, rosa, ciano, arancio, teal, indaco, lime, grigio)
- **Card tecnici colorate in dashboard** — il riepilogo tecnici usa il colore anagrafica (`border-top` + header semitrasparente + pallino), coerente con la lista interventi

---

## [0.5.2] — 2026-05-15

### Corretto
- **Eliminazione cliente bloccata** se esistono interventi collegati: messaggio d'errore con conteggio e redirect alla scheda cliente
- **Form modifica intervento**: se il cliente è stato soft-deleted non compare più nella lista, il link veniva azzerato silenziosamente al salvataggio; ora il cliente eliminato appare in cima al dropdown marcato in rosso con `[eliminato]`
- **Lista interventi**: aggiunto badge `el.` rosso accanto al nome del cliente soft-deleted; `conDettagli()` ora include `cliente_deleted_at` dalla JOIN

---

## [0.5.1] — 2026-05-15

### Corretto
- Nome cliente nelle schede intervento e nella lista tecnico: le ditte/società mostravano nome+cognome invece della ragione sociale; ora il campo `ragsoc` ha la precedenza su nome/cognome in tutte le viste

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
