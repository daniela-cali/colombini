# Changelog

Tutte le modifiche significative al progetto sono documentate in questo file.

Il formato segue [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
e il progetto adotta il [Versionamento Semantico](https://semver.org/lang/it/) `MAJOR.MINOR.PATCH`.

## [0.29.0] — 2026-06-10

### Aggiunto
- **Clienti — campo `nazione`** — nuova colonna `VARCHAR(50) NOT NULL DEFAULT 'ITALIA'`; select nel form create/edit alimentata da `NAZIONI` const nel controller (`ITALIA`, `FRANCIA`, `NON VALORIZZATO`); validazione `in_list` lato server; `NON VALORIZZATO` usato come fallback per i record importati senza nazione
- **Import CSV clienti — miglioramenti** — riconoscimento tipo da campo `perfis` (S = persona fisica, N = società); split automatico nome unico su primo spazio → `cognome` + `nome` per le persone fisiche; deduplicazione record per codice prima dell'import; sostituzione `upsertBatch` con loop insert/update individuale (risolve bug SQL CI4); risultato separato in `inseriti` / `aggiornati` / `saltati`; select di mappatura rimuove dal DOM le opzioni già assegnate per evitare duplicati
- **Import CSV — `nazione` in `CAMPI_DEST`** — il campo nazione è ora mappabile dall'interfaccia di import

### Corretto
- **Geocodifica massiva — rate limit Nominatim** — delay tra le chiamate portato da 300 ms a 1100 ms per rispettare il limite di 1 req/sec; ricarica automatica della pagina a completamento per aggiornare i contatori
- **`ResetDatiTest` seeder** — aggiunta `clienti` alla lista delle tabelle da azzerare

## [0.28.1] — 2026-06-10

### Corretto
- **`MagMovimentiModel` — normalizza()** — aggiunto callback `$beforeInsert` che gestisce il default `data = oggi` e la null-coercion per `num_documento`, `fornitore_id`, `cliente_id`, `note`; `MagMovimenti::store()` usa ora `array_merge(getPost(), ['user_id' => ...])` invece di array esplicito campo per campo

## [0.28.0] — 2026-06-10

### Aggiunto
- **Calendario — `data_entro` nelle card** — gli eventi del calendario e le card del pool mostrano "Entro gg/mm" sulla seconda riga quando la scadenza è impostata; visibile anche nel modal di dettaglio evento
- **Calendario — banner scadenze aperte** — sopra il calendario compare un banner `alert-warning` con tutti gli interventi aperti che hanno una scadenza impostata, ordinati per urgenza; ogni chip è un link diretto alla scheda intervento

### Corretto
- **`data_entro` non veniva salvata** — `store()` e `update()` in `Interventi.php` costruivano l'array campi manualmente escludendo il campo; refactoring completo: entrambi i metodi usano ora `$this->request->getPost()` diretto
- **`InterventoModel` — normalizza()** — aggiunto callback `$beforeInsert`/`$beforeUpdate` che gestisce: conversione `data_pianificata` da formato HTML (`Y-m-d\TH:i`) a MySQL, null-coercion per tutti i campi opzionali (incluso `data_entro`), `geocoded_at` automatico

## [0.27.0] — 2026-06-10

### Aggiunto
- **Portale — auto-fill richiedente e telefono** — nome e telefono del richiedente vengono pre-compilati dall'anagrafica cliente; il portale rimane aggiornato al cambio cliente
- **Richiesta portale → Intervento** — pulsante "Crea intervento" nella scheda richiesta; pre-compila tipo, luogo, cliente e descrizione; aggiorna lo stato richiesta a "In lavorazione" al salvataggio
- **Refactoring `intervento_materiali_note`** — drop della vecchia tabella `intervento_materiali`; nuova tabella `intervento_materiali_note` con scope cliente (`cliente_id NOT NULL`), campo `stato` (0 = da portare / 1 = fornito), `solo_note`, `note TEXT`, `movimento_id` per futura integrazione scarico; FK `intervento_id` con `ON DELETE SET NULL` (le righe sopravvivono all'eliminazione dell'intervento)
- **Scheda cliente — Materiali & Note** — nuova card con 3 tab: Note libere / Da portare / Già portati; form inline con Select2 per selezione articolo (quantità visibile solo se articolo selezionato) e campo note; eliminazione riga da portare con conferma
- **Intervento — campo "Entro il"** — nuova colonna `data_entro DATE NULL` nella tabella `interventi`; campo nel form create/edit con 4 pulsanti quick-select (Oggi / Domani / Fine sett. / Sett. prox); nella scheda intervento badge colorato in rosso (scaduto), giallo (≤ 2 giorni), grigio (resto)
- **Crea intervento da scheda cliente** — il pulsante "Nuovo intervento" nella scheda cliente passa `?cliente_id=X` pre-compilando il select cliente nel form di creazione
- **Modale chiusura — step materiali** — rimpiazzato il vecchio form dinamico con checkboxes dei materiali "da portare" legati all'intervento; salto automatico allo step se ci sono materiali pendenti; righe aggiuntive con Select2 per i materiali consegnati sul momento

### Corretto
- **Scheda cliente — storico interventi** — il pulsante "Nuovo intervento" appare sempre, anche quando il cliente non ha ancora interventi (rimosso il wrapper condizionale `if (!empty($interventi))`)
- **Chiusura intervento** — la query di aggiornamento stato materiali include sempre il filtro `intervento_id = X` per evitare di marcare note di altri interventi
- **`InterventoMaterialiNoteModel::normalizza`** — visibilità cambiata da `private` a `protected` (richiesto da CI4 per i callback `beforeInsert`/`beforeUpdate`)

## [0.26.0] — 2026-06-09

### Aggiunto
- **Materiali forniti negli interventi** — al momento della chiusura il tecnico viene guidato con un prompt "Hai consegnato materiali?"; se sì si apre un form multi-riga con Select2 (optgroup "Abituali per questo cliente") per registrare articoli e quantità; salvati in `intervento_materiali` in transazione con la chiusura
- **Scheda intervento — sezione materiali** — nella view `show` dell'intervento completato i materiali registrati sono elencati nella card dettagli
- **Rapportino PDF — sezione materiali** — il PDF include la tabella dei materiali forniti tra le note di chiusura e le firme
- **Migration `intervento_materiali`** — tabella con FK su `interventi` (CASCADE), `mag_articoli` e `mag_movimenti` (nullable, per futura generazione scarico automatico)
- **`InterventoMaterialiModel`** — metodi `perIntervento()` e `abitualiPerCliente()` per recupero materiali e storico clienti
- **Categorie magazzino — ordinamento drag-and-drop** — handle `⋮⋮` nella lista categorie; l'ordine viene salvato via AJAX con feedback visivo (spinner → check); disponibile solo con 2+ categorie

### Corretto
- **Categorie magazzino — normalizzazione nome** — cambiato da `strtoupper` a `ucwords` per evitare problemi visivi (es. "Prodotti Chimici" anziché "PRODOTTI CHIMICI")

## [0.25.0] — 2026-06-09

### Aggiunto
- **Posizioni scaffale — mini CRUD** — pagina in Impostazioni per creare, modificare ed eliminare le posizioni fisiche del magazzino; blocco eliminazione se articoli collegati; card nella home Impostazioni
- **Select2 nel form movimenti** — la select articolo usa ora Select2 con ricerca testuale; stile allineato al form-control-sm di Bootstrap 4 tramite `custom.css`

### Modificato
- **Uppercase automatico magazzino** — `MagArticoliModel`, `MagCategorieModel`, `MagPosizioniModel` normalizzano in uppercase i campi testo (`descrizione`, `cod_articolo`, `unita_misura`, `coordinate`, `nome`) tramite callback `beforeInsert`/`beforeUpdate`

## [0.24.0] — 2026-06-09

### Modificato
- **Refactoring controller Impostazioni** — il monolite `Impostazioni.php` è stato suddiviso in 3 controller sotto `app/Controllers/Impostazioni/`: `Generale` (index, parametri, geocodifica), `Utenti` (utenti app + portale), `Magazzino` (categorie). Route aggiornate con namespace `Impostazioni\*`. Il vecchio file è stato eliminato.
- **Impostazioni index** — pagina riorganizzata in sezioni visive (Configurazione generale / Utenti / Magazzino) con intestazioni tipografiche e bordi separatori

## [0.23.0] — 2026-06-09

### Aggiunto
- **Categorie magazzino — mini CRUD** — pagina in Impostazioni per creare, modificare ed eliminare le categorie (`mag_categorie`); form integrato nella stessa pagina con doppio stato aggiungi/modifica; blocco eliminazione se categoria ha articoli collegati; card nella home Impostazioni

## [0.22.0] — 2026-06-08

### Aggiunto
- **Sconti fornitore su articoli** — migration `perc_sconto_1/2/3` nullable su `mag_articoli`; form create/edit con 3 input sconti e prezzo netto calcolato live in JS; scheda articolo mostra sconti a catena e prezzo netto; form nuovo movimento con colonne Listino/Sc.1%/Sc.2%/Sc.3%/Costo netto auto-calcolate per riga (auto-fill da dati articolo)
- **MagazzinoOldDataSeeder** — importa 14 fornitori e 1186 articoli dal vecchio gestionale (`doc/magColombini/magColombini.sql`) con mapping categorie e sconti fornitore

### Modificato
- **Refactoring controller CRUD** — normalizzazioni dati (cast, nullable, uppercase) spostate nei model con callback `beforeInsert`/`beforeUpdate`; controller `store()`/`update()` ora passa `$request->getPost()` direttamente al model
- **Magazzino index** — colonna Posizione spostata prima di Categoria; coordinate visibili in lista; select articolo nel form movimenti ridotta in larghezza
- **Magazzino show** — Posizione mostrata come badge in evidenza sotto il nome articolo (rimossa dalla tabella)

### Corretto
- **Flashdata duplicati** — rimossi i blocchi `success`/`error` ridondanti da 7 view (magazzino e fornitori) che li mostravano due volte rispetto al layout
- **CLAUDE.md** — aggiunte regole: brainstorming prima di implementare, pattern controller/model, flashdata gestiti dal layout

## [0.21.0] — 2026-06-08

### Aggiunto
- **Movimenti magazzino — modulo completo** — controller `MagMovimenti` con index (filtri tipo/data), create, store (transazione atomica insert + aggiornamento giacenze), show (dettaglio con totale DDT), delete (transazione inversa con ripristino giacenze)
- **Form carico guidato da fornitore** — selezionando il fornitore gli articoli associati salgono in cima agli `<select>` tramite optgroup JS senza AJAX; colonna costo unitario visibile solo per carico/inventario
- **`MagMovimentiRigheModel`** — model per le righe con metodo `perMovimento()`
- **Sidebar** — voce "Movimenti" aggiunta sotto Magazzino (solo staff); link "Articoli" con rilevamento attivo corretto per non accendersi su `/magazzino/movimenti`

## [0.20.0] — 2026-06-08

### Aggiunto
- **Fornitori — modulo completo** — controller `Fornitori` con CRUD (index/show/create/edit/delete con blocco se ha articoli collegati); toggle azienda/privato nel form; 4 view + 4 file help
- **Magazzino — modulo articoli** — controller `Magazzino` con CRUD; filtro per categoria nella lista; avvisi sotto-scorta evidenziati; scheda articolo con storico movimenti; giacenza non modificabile da form (solo da movimenti); 4 view + 4 file help
- **Routes** — gruppi `/fornitori` e `/magazzino` sostituiscono il vecchio stub magazzino; entrambi con filtro `solo-staff`

## [0.19.0] — 2026-06-08

### Aggiunto
- **Magazzino — migration tabelle** — nuove tabelle `fornitori`, `mag_categorie` (con dati iniziali: Ricambi piscine, Ricambi addolcitori), `mag_posizioni` (9 zone fisiche dal vecchio sistema), `mag_articoli`, `mag_movimenti`, `mag_movimenti_righe`; tutti i campi documentati con commenti MySQL
- **Anagrafiche — Fornitori** — voce Fornitori aggiunta alla sezione Anagrafiche nella sidebar (route `/fornitori`, coming soon)
- **Anagrafiche — Tecnici spostati** — Tecnici spostato da Sistema ad Anagrafiche; route rinominata da `/sistema/tecnici` a `/tecnici`; tutti i link e redirect aggiornati
- **Magazzino — sidebar semplificata** — sostituiti i dropdown annidati (Piscine/Trattamento/Prodotti chimici) con un'unica voce "Articoli" → `/magazzino`

---

## [0.18.0] — 2026-05-31

### Aggiunto
- **Viaggi — ricerca per numero** — campo di ricerca per ID viaggio con messaggio contestuale se non trovato
- **CSS — input number senza freccette** — rimossi i controlli spin button dagli input `type=number` su tutti i browser

## [0.17.0] — 2026-05-30

### Aggiunto
- **Viaggi — filtro per periodo** — sostituita la navigazione settimanale con due datepicker dal/al ad auto-submit; default settimana corrente; pulsante "Torna a oggi" per tornare alla settimana corrente; numero viaggio (`#id`) visibile nella riga di ogni viaggio

---

## [0.16.2] — 2026-05-30

### Corretto
- **Impostazioni — messaggio errore utente portale** — i numeri di viaggio nel messaggio di blocco cancellazione sono ora link cliccabili che aprono direttamente la scheda viaggio; aggiunto flash key `error_html` nel layout per messaggi con HTML controllato

---

## [0.16.1] — 2026-05-30

### Corretto
- **Impostazioni — eliminazione utente portale** — bloccata se il cliente associato ha interventi inseriti in uno o più viaggi; messaggio d'errore con ID dei viaggi coinvolti; in precedenza la FK `fk_tappe_intervento` causava un'eccezione non gestita

---

## [0.16.0] — 2026-05-29

### Aggiunto
- **Calendario — rimozione pianificazione** — bottone × su ogni evento del calendario per annullare l'assegnazione; chiede conferma, chiama `annullaPianificazione` via fetch e ricarica la pagina per aggiornare anche il pool
- **Calendario — ordinamento pool per distanza** — gli interventi nel pool sono ordinati per priorità (urgente → ordinario → programmato) e, a parità di priorità, per distanza dalla sede aziendale calcolata con la formula di Haversine; i clienti non geocodificati vanno in fondo al loro gruppo; la distanza è visualizzata su ogni card del pool

---

## [0.15.0] — 2026-05-29

### Aggiunto
- **Interventi — ricerca full-text** — campo di ricerca in tempo reale nella pagina interventi: filtra righe su tutte le card-gruppo (id, cliente, tipo, luogo, descrizione, data, stato) e nasconde le card senza risultati; implementato in JS vanilla con `textContent`

### Corretto
- **Interventi — testo header card tecnico** — il nome tecnico nell'header delle card era bianco su sfondo quasi trasparente; la regola CSS è stata ristretta alle sole card con classe di colore esplicita (`card-primary`, `card-info`, ecc.)

---

## [0.14.0] — 2026-05-29

### Aggiunto
- **Impostazioni — utenti portale** — CRUD completo per gli utenti con accesso al portale cliente: elenco, creazione (username + password), modifica (username e/o password), eliminazione; lo username funge anche da email identity Shield (`username@portale.colombini-snc.it`) e viene aggiornato in modo coerente

---

## [0.13.0] — 2026-05-29

### Aggiunto
- **Viaggi — visualizzazione settimanale** — la pagina viaggi mostra l'intera settimana con navigazione prev/next; i viaggi sono raggruppati per giorno con una card per ogni giornata e pulsante PDF inline; `ViaggioModel::perRange()` per query su range di date

### Modificato
- **Interfaccia — card senza outline** — rimosse le classi `card-outline` dalle card con header colorato; i bottoni nelle `card-tools` ereditano automaticamente il colore della card tramite CSS custom properties (`--card-accent`)

---

## [0.12.1] — 2026-05-28

### Corretto
- **Eliminazione tecnico** — bloccata se il tecnico ha viaggi associati (qualsiasi stato); messaggio d'errore con conteggio e redirect alla scheda; in precedenza la FK `fk_viaggi_tecnico` causava un'eccezione non gestita

---

## [0.12.0] — 2026-05-28

### Aggiunto
- **Richieste — elenco admin** — pagina `/richieste` con tutte le richieste aperte (nuova + in lavorazione), ordinate dalla più vecchia; righe colorate per anzianità (giallo ≥3gg, rosso ≥7gg); anteprima nota troncata; click sulla riga apre il dettaglio; click sul nome cliente apre la scheda con `?from=richieste`
- **Richieste — dettaglio e thread** — visualizzazione ad albero: nodo radice con la richiesta originale del cliente, risposte staff indentate con bordo azzurro; messaggi futuri del cliente allineati al nodo radice; form "Rispondi" per aggiungere un messaggio e/o cambiare stato (in lavorazione / chiusa); form nascosto se richiesta chiusa

- **Richieste — thread portale** — il cliente vede le stesse risposte dello staff nella scheda richiesta del portale, con stesso stile ad albero (read-only)
- **Richieste — tabella `richieste_messaggi`** — nuova tabella per il thread messaggi (richiesta_id, user_id, testo, created_at); predisposta per thread multi-messaggio futuro
- **Magazzino — struttura sidebar** — sezione Magazzino ristrutturata: Prodotti chimici + Piscine (Impianti/Ricambi) + Trattamento Acqua (Impianti/Ricambi); route parametrizzate `magazzino/{tipo}/{categoria}` con controller coming soon
- **Sidebar — ristrutturazione completa** — Ottimizzazione e Pianificazione nascoste (route intatte); Impianti come link diretti senza dropdown; Sistema sciolto in 4 link diretti con icone parlanti (fa-hard-hat, fa-tags, fa-truck, fa-cog); Quaderni rimosso

- **Scheda cliente — navigazione contestuale** — breadcrumb e pulsante X gestiscono `?from=richieste`; card anagrafica con header "Scheda cliente" e X di ritorno

---

## [0.11.0] — 2026-05-28

### Aggiunto
- **Dashboard admin — widget cliccabili** — i 4 info-box (da pianificare, pianificati, in corso, completati mese) sono link diretti all'elenco interventi filtrato per stato
- **Dashboard admin — richieste portale** — sezione compatta in fondo con badge "N nuove" e tabella delle ultime 5 richieste
- **Sidebar — Richieste** — nuova voce sotto Assistenza (solo staff) con badge contatore richieste nuove in tempo reale
- **Pianificazione rapida — zona giornata** — la pagina pianificazione sostituisce le colonne per tecnico con un'unica zona "Giornata del [data]"; barra settimanale con contatori interventi per giorno
- **Pianificazione rapida — assegna al drag** — trascinando un intervento nella giornata si apre un modal: tecnico consigliato via API, data/ora precompilata con orario stimato; salvataggio immediato via `POST /interventi/{id}/pianifica`
- **Pianificazione rapida — orario suggerito** — nuova API `GET /interventi/api/orario-suggerito`: calcola l'orario libero successivo per il tecnico selezionato tenendo conto di orari di lavoro (`tecnici_orari`), durate stimate dei tipi intervento (`durata_default`) e pausa pranzo; si aggiorna dinamicamente al cambio tecnico nel modal
- **Pianificazione rapida — annulla pianificazione** — bottone X sulle card già pianificate nella giornata; chiama `POST /interventi/{id}/annulla-pianificazione` che riporta lo stato a `da_pianificare`
- **VRP — Competenze tecnici** — tabella `tecnici_competenze` (FK su users e tipi_intervento, livelli: Apprendista / Autonomo / Referente); form inline nella scheda tecnico per assegnare il livello per ogni tipo intervento
- **VRP — Requisiti veicoli** — colonne `cambio_automatico` e `carico_massimo` su `veicoli`; flag `richiede_cambio_auto` su `users`; form aggiornati per tecnici e veicoli
- **VRP — Geocodifica clienti (Fase 2)** — colonna `geocodifica_fallita` su `clienti`; badge colorati nell'elenco e scheda clienti (rosso = mai tentato, arancio = fallito); utility in Impostazioni con barra progresso AJAX passo-passo, contatori riepilogo e opzione "Riprova falliti"
- **VRP — Sezione Viaggi e tabelle DB (Fase 3)** — nuove tabelle `viaggi` e `viaggi_tappe`; colonne `priorita` (urgente/ordinario/programmato), `fissato`, `ora_inizio` su `interventi`; nuovo stato `da_pianificare`; `veicolo_id` opzionale su `users`; controller/views Viaggi con elenco per data, dettaglio tappe, autorizzazione e annullamento viaggio; voce "Viaggi" in sidebar
- **Geocodifica** — elenco clienti con indirizzo non trovato nella utility, con link diretto alla modifica; redirect automatico alla pagina di provenienza dopo il salvataggio (`_from` hidden field)
- **VRP — Ottimizzazione automatica percorsi (placeholder)** — controller `Ottimizzazione` + view "prossimamente" che conserva tutta l'infrastruttura ORS/Vroom già sviluppata; `VrpService` e `AssegnazioneService` intatti pronti per il rilascio futuro; voce "Ottimizzazione" in sidebar con badge Beta
- **Pianificazione manuale** — nuova interfaccia drag & drop: board orizzontale con colonna "Da pianificare" e una colonna per ogni tecnico; badge iniziali colorati; suggerimento tecnici competenti per tipo intervento (livello ≥ Autonomo); selezione data viaggio; salvataggio bozze via JSON POST; `pianificazione.css` separato per gli stili del board
- **Form creazione intervento** — aggiunti selettori Stato (`da_pianificare` default) e Priorità; competenze nella scheda tecnico già disponibili al momento della creazione
- **Demo dati** — `demo_clienti.csv` (25 clienti zona Ceriale SV, importabile); `demo_interventi.sql` (15 interventi) e `demo_interventi_2.sql` (15 interventi aggiuntivi) collegati ai clienti demo via subquery su codice
- **Elenco viaggi — raggruppamento** — sezione Bozze (giallo) e sezione Approvati (verde); contatore tappe e veicolo già visibili in lista; avviso se veicolo mancante; pulsante Stampa giornata PDF (solo se ci sono approvati)
- **Tipologia veicolo** — campo `tipo` VARCHAR(50) su `veicoli` (Autovettura / Autocarro / Camion); costante `VeicoloModel::TIPI`; migration `2026-05-19-100000_AddTipoToVeicoli`; colonna in elenco veicoli e select nel form
- **Riapri viaggio** — azione che riporta un viaggio approvato in bozza senza toccare tappe e interventi; il veicolo assegnato viene conservato
- **Blocco pianificazione su approvati** — il board mostra un badge con link al viaggio per i tecnici con viaggio già approvato; `salva()` rifiuta lato server l'invio per quei tecnici
- **PDF foglio di viaggio** — stampa per singolo tecnico (`/viaggi/{id}/pdf`): intestazione aziendale, info viaggio, tabella tappe con orari stimati; uso interno, senza spazio firma
- **PDF riepilogo giornata** — stampa di tutti i viaggi approvati del giorno (`/viaggi/pdf/{data}`): blocco colorato per tecnico + tabella tappe
- **Helper `date_ita`** — converte date Y-m-d in formato italiano con giorno localizzato (es. "Lunedì 19/05/2026"); usato in tutte le view che mostrano date
- **Targa veicolo** — visualizzata concatenata al nome in tutto il modulo viaggi
- **Restrizioni accesso tecnici** — filtro `solo-staff` applicato alle route pianificazione, ottimizzazione, viaggi, preventivi, report e configurazione (sistema + impostazioni): i tecnici vengono reindirizzati in dashboard
- **Sidebar per ruolo** — i tecnici vedono solo le sezioni accessibili (Dashboard, Calendario, Clienti, Impianti, Interventi, Magazzino); riorganizzazione sezioni: Pianificazione e Viaggi sotto Assistenza, Preventivi in nuova sezione Commerciale
- **Doppio ruolo admin+tecnico** — flag `assegnabile_interventi` su `users`: un admin (o staff/operativo) con questo flag attivo compare nelle liste tecnici, può essere assegnato agli interventi e ha accesso alla scheda tecnico (orari, competenze, veicolo)
- **Firma touch cliente** — raccolta firma digitale al completamento dell'intervento: canvas con signature_pad (dito/stilo), salvataggio base64 PNG nel DB (`firma_cliente` + `firma_at`); anteprima firma nella scheda intervento; firma incorporata nel PDF rapportino
- **Firma touch tecnico** — il tecnico può firmare (o dichiarare presa visione) direttamente nella modal di chiusura intervento; firma salvata in `firma_tecnico` + `firma_tecnico_at`; visualizzata in scheda e PDF accanto alla firma cliente
- **Presa visione** — fallback testuale per entrambe le firme: se il canvas non viene usato è disponibile una checkbox "presa visione" che registra la conferma senza immagine
- **Dashboard tecnico — info-box cliccabili** — ogni widget filtra la pagina interventi per stato e tecnico; contatori calcolati sui soli interventi del tecnico loggato
- **Dashboard tecnico — interventi da assegnare** — gli interventi `da_pianificare` non assegnati appaiono in cima alla tabella con pulsante "Assegna a me"
- **Dashboard tecnico — pulsante "Tutti i miei interventi"** — link diretto all'elenco filtrato per tecnico
- **Viaggi — filtro automatico per tecnico** — se l'utente è tecnico, la pagina viaggi mostra solo i propri viaggi
- **Elenco interventi — colonna Cliente prima di Tipo** — riordinamento colonne per leggibilità immediata
- **Intervento rapido dalla dashboard tecnico** — pulsante "Nuovo intervento" apre modal con cliente, tipo e data opzionale; pre-compila tecnico loggato e stato pianificato; redirect diretto alla scheda dopo il salvataggio
- **Tecnico consigliato** — nel form di creazione intervento, selezionando tipo e cliente appare un badge con il tecnico che ha gestito più spesso quella combinazione (fallback su tipo solo se nessun dato per cliente)
- **Pianificazione rapida — griglia settimanale** — la schermata pianificazione sostituisce la timeline giornaliera con una griglia a 7 colonne (lun–dom); navigazione settimana con prev/next e date picker; drag & drop dalla pool verso qualsiasi giorno; le card già pianificate mostrano tecnico e ora; contatori per giorno nella barra di navigazione
- **Pianificazione rapida — tecnico suggerito con fallback disponibilità** — se nessun tecnico risulta dallo storico, l'API cerca il tecnico con meno interventi nella data selezionata tra quelli con competenza ≥ Autonomo; la modal indica la fonte (storico / disponibilità); tendina tecnici raggruppata per livello competenza (Autonomi/Referenti, Base, Non competenti)
- **Dashboard tecnico — interventi raggruppati per data** — la tabella "I miei prossimi interventi" è raggruppata per giorno con separatori visivi; colonna "Assegnato" rimossa; mostrati solo gli interventi del tecnico loggato (no interventi altrui)
- **Elenco interventi — completati nascosti di default** — i completati e annullati non appaiono nell'elenco standard; toggle "Mostra anche completati" / "Nascondi completati" per visualizzarli
- **Scheda cliente — storico interventi** — sezione in fondo alla scheda con tabella degli interventi collegati (data, tipo, tecnico, stato); pulsante "Nuovo" precompila il cliente; link a ogni intervento con ritorno contestuale alla scheda
- **Navigazione contestuale `?from=clienti/ID`** — dalla scheda cliente, il link a un intervento porta `?from=clienti/ID`; nella scheda intervento il breadcrumb mostra "Clienti › Scheda cliente" e il pulsante "Elenco" diventa "Scheda cliente" con link di ritorno corretto
- **Elenco clienti — DataTables** — ricerca live client-side con DataTables (Bootstrap 4); rimosso form di ricerca server-side; paginazione 25/50/100/Tutti; ordinamento per colonna; interfaccia in italiano
- **Calendario — filtro tecnico** — barra pulsanti sopra il calendario per filtrare gli eventi per tecnico; "Tutti" mostra tutti; API `calendario/eventi` estesa con parametro `tecnico_id`
- **Calendario — genera viaggio giornata** — click su un giorno del calendario (corpo o header colonna) lo evidenzia e abilita il pulsante "Genera viaggio"; crea automaticamente i record `viaggi` + `viaggi_tappe` in stato autorizzato per tutti i tecnici con interventi pianificati in quella data; redirect al PDF riepilogo giornata esistente; apertura in nuova tab su desktop
- **X contestuale — btn-tool** — pulsante di chiusura con `from` in `interventi/show` e `interventi/edit` usa la classe AdminLTE `btn-tool` invece di `btn-outline-secondary`
- **Calendario — pool interventi da pianificare** — sidebar sinistra con card raggruppate per tipo (accordion collassato); drag & drop FullCalendar.Draggable verso il calendario; modal pianifica con orario pre-compilato dall'orario di drop, select tecnico con optgroup per livello competenza, suggerimento tecnico via API (referente → storico → disponibile), orario consecutivo aggiornato al cambio tecnico (solo se più tardi del drop); conferma salva via `POST /interventi/{id}/pianifica`, rimuove la card dal pool e aggiorna i contatori gruppo e totale
- **Calendario — tecnico consigliato — priorità referente** — nuova logica: prima si propone il Referente (livello 3) meno occupato nel giorno (escluso se ha già ≥ 4 interventi), poi chi ha più storico completato sul tipo, infine il meno occupato con competenza ≥ 2; label sempre "Consigliato" (rimosso "Meno occupato") in calendario e pianificazione
- **Calendario — città negli eventi** — campo `citta` del cliente visibile nei tooltip (Cliente · Città · Tecnico · Tipo) e come riga secondaria nelle card degli eventi FC; nelle pool card mostra la città del cliente (fallback sul luogo intervento)
- **CLAUDE.md — regola commenti metodi PHP** — aggiunta linea guida: ogni metodo di controller o model deve avere un commento descrittivo sopra che spieghi cosa fa e perché
- **Calendario — sidebar ridimensionabile** — drag handle tra pool e calendario; la larghezza viene salvata in `localStorage` e ripristinata al ricaricamento; stili spostati in `public/css/calendario.css`
- **Calendario — giorno odierno evidenziato in header** — sfondo giallo e lineetta arancione sotto la data tramite `::after` sulla cella intestazione del giorno corrente

### Corretto
- **Firma email** — sfondo trasparente illeggibile in dark mode: template HTML completo con `bgcolor` su body e tabella wrapper, `color-scheme: light only` per forzare tema chiaro nei client che lo supportano
- **Invio email — messaggio errore** — errore generico sostituito con messaggio specifico per problemi di connettività SMTP vs configurazione
- **Invio email — overlay attesa** — aggiunto spinner a schermo intero durante l'invio per evitare doppi click
- **Pianificazione — modal pool** — descrizione intervento assente dalla modal: aggiunto `descrizione` alla SELECT della `queryInterventi`
- **Geocodifica massiva** — loop infinito in modalità "Riprova falliti": aggiunto tracking `after_id` per avanzare sempre per ID crescente anche quando un cliente rimane nello stato fallito
- **Creazione tecnico** — pausa pranzo non precompilata con i valori di configurazione default; ora viene valorizzata correttamente alla prima apertura
- **Colore tecnico** — vincolo unicità con validazione CI4 `is_unique`; color picker mostra solo colori ancora liberi
- **ORS profilo veicolo** — errore "Invalid profile: car." risolto aggiungendo `'profile' => 'driving-car'` ai veicoli in `AssegnazioneService`
- **`Impostazioni::editUtenteApp`** — rimosso tipo di ritorno `: string` incompatibile con `RedirectResponse`
- **`Viaggi::show()`** — rimosso `: string` incompatibile con `RedirectResponse`
- **Approvazione viaggio** — bloccata se nessun veicolo è assegnato; il salvataggio veicolo verifica che l'ID non sia null
- **Veicolo in bozza ricreata** — quando `salva()` in pianificazione sovrascrive una bozza esistente, il veicolo già assegnato viene conservato
- **Colori PDF** — accent cambiato da teal `#0d9488` a blu `#2980b9` su rapportino intervento, foglio di viaggio e riepilogo giornata
- **Rapportino PDF** — nome azienda nascosto quando è presente il logo (solo logo + indirizzo)

---

## [0.10.1] — 2026-05-17

### Corretto
- **Adeguamento layout AdminLTE** — card-header con `.card-tools` (usa `margin-left: auto` nativo) al posto di `d-flex justify-content-between`; card-footer con `clearfix` + `float-left`/`float-right` per l'allineamento pulsanti Annulla/Salva; applicato su 22 view (clienti, tecnici, interventi, dashboard, impostazioni, sistema)
- **Login** — attributi `autocomplete` corretti sui campi credenziali

---

## [0.10.0] — 2026-05-17

### Aggiunto
- **Censimento veicoli aziendali** — anagrafica veicoli (nome, targa) in Sistema / Configurazione: CRUD completo con migration, model, controller e views; voce "Veicoli" nella sidebar; targa univoca salvata automaticamente in maiuscolo

---

## [0.9.0] — 2026-05-16

### Aggiunto
- **Firma email HTML** — layout a colonna con logo aziendale, sottotitolo, indirizzo, telefono e sito web; etichette testo (Ind. Tel. Web) al posto delle icone
- **Logo aziendale** — upload PNG/JPG/SVG dalla pagina Impostazioni; usato nella firma email (URL pubblico) e nel rapportino PDF (base64 inline per Dompdf)
- **Campi telefono e sito web** nelle impostazioni aziendali
- **Email in modalità HTML** — `setMailType('html')` con corpo formattato e firma integrata
- **Nome cliente nell'oggetto email** — es. `Rapportino intervento #2 - Martiri Srl - Colombini Snc`

### Corretto
- Commenti debug CI4 (`<!-- DEBUG-VIEW START -->`) rimossi dalle email con `['debug' => false]`
- URL senza `index.php` — `App::$indexPage = ''`
- Estensione PHP `gd` abilitata per il rendering immagini PNG in Dompdf

---

## [0.8.9] — 2026-05-16

### Aggiunto
- **Rapportino PDF** — generazione con Dompdf (intestazione azienda, dati intervento, note di chiusura, spazio firme); bottone "PDF" apre in nuova tab
- **Invio email rapportino** — modal con email destinatario pre-compilata dall'anagrafica cliente; PDF allegato; CC all'indirizzo aziendale; bottone "Invia" nella scheda intervento
- **Configurazione SMTP** via `.env` (non committato)

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
