# Roadmap — Colombini SNC Gestionale

*Stato attuale: v0.28.0 (prossimo) — Visite in abbonamento e interventi programmati.*
*Ultimo aggiornamento: 2026-06-10*
*Target go-live (v1.0.0): fine settembre 2026*

---

## 0.19 — 0.21 · Pianificazione avanzata

| Feature | Note |
|---|---|
| **Zone geografiche nel pool calendario** | Raggruppamento/etichettatura visiva degli interventi per area — chiarire insieme cosa si intende esattamente |
| **Ottimizzazione VRP — sblocco UI** | `VrpService` e `AssegnazioneService` già pronti; controller `Ottimizzazione` ha `genera()` e `salva()` implementati — togliere il "prossimamente" e collegare la view |
| **Preventivi — funzionalità reale** | Attualmente solo stub; aggiungere voci, totali, PDF, invio email al cliente |

---

## 0.20 — 0.25 · Magazzino & Impianti *(priorità alta — sblocca i rapportini completi)*

| Feature | Note |
|---|---|
| ~~**Migration tabelle magazzino**~~ | ✅ Completato in v0.19.0 |
| ~~**Fornitori — CRUD completo**~~ | ✅ Completato in v0.20.0 |
| ~~**Magazzino — CRUD articoli**~~ | ✅ Completato in v0.20.0 |
| ~~**Magazzino — movimenti**~~ | ✅ Completato in v0.21.0 (carico/scarico/rettifica/inventario; consegna da intervento rimandato) |
| ~~**Sconti fornitore + seeder dati storici**~~ | ✅ Completato in v0.22.0 |
| ~~**Categorie magazzino — mini CRUD**~~ | ✅ Completato in v0.23.0 |
| ~~**Posizioni scaffale — mini CRUD**~~ | ✅ Completato in v0.25.0 |
| **Magazzino — prodotti chimici** | Categoria da aggiungere quando disponibile; struttura già pronta |
| ~~**Intervento → consumo materiali**~~ | ✅ Completato in v0.26.0 (prompt chiusura + Select2 + storico cliente) |
| ~~**Rapportino PDF — sezione materiali**~~ | ✅ Completato in v0.26.0 |
| **Impianti — censimento piscine** | CRUD impianto per cliente (tipo, anno installazione, caratteristiche); collegato alla scheda cliente |
| **Impianti — trattamento acqua** | Sotto-sezione separata nella sidebar (già strutturata) |

## 0.27 · Portale & Materiali previsti

| Feature | Note |
|---|---|
| ~~**Portale — auto-fill richiedente e telefono**~~ | ✅ Completato in v0.27.0 — pre-compilato da anagrafica cliente |
| ~~**Richiesta portale → intervento**~~ | ✅ Completato in v0.27.0 — bottone "Crea intervento" nella scheda richiesta; pre-compila tipo/luogo/descrizione; aggiorna stato richiesta |
| ~~**`intervento_materiali_note` — refactoring e nuova tabella**~~ | ✅ Completato in v0.27.0 — nuova tabella con scope cliente, stato lifecycle (da portare→fornito), sezione Materiali & Note nella scheda cliente, step materiali nella modale chiusura, campo `data_entro` negli interventi |

## 0.28 — 0.30 · Visite in abbonamento *(priorità alta — generano interventi programmati)*

| Feature | Note |
|---|---|
| **Scheletro abbonamenti** | Contratti di manutenzione periodica per cliente: frequenza, tipo intervento, periodo validità |
| **Generazione automatica interventi programmati** | Gli abbonamenti generano automaticamente interventi con stato `da_pianificare` alle scadenze previste |
| **Materiali da portare alla visita successiva** | Alla chiusura il tecnico indica i materiali per la visita successiva — salvati in `intervento_materiali_previsti` dell'intervento corrente e trasferiti automaticamente al successivo generato dall'abbonamento |
| **Promemoria materiali nel rapportino/pianificazione** | Al momento della pianificazione della visita successiva, i materiali indicati in precedenza sono visibili al tecnico |

---

## 0.29 — 0.32 · Report & Completamento pre-go-live

| Feature | Note |
|---|---|
| **Report interventi** | Produttività tecnici, interventi per tipo/periodo, tempo medio chiusura |
| **Report clienti** | Frequenza interventi, storico impianti |
| **Report magazzino** | Consumi, soglie riordino |
| **Portale cliente — scheda impianto** | Il cliente può vedere i propri impianti e richiedere assistenza su uno specifico |
| **Notifiche email automatiche** | Promemoria appuntamento al cliente, notifica chiusura intervento |

---

## 1.0.0 · Go-live con dati reali

> Switch da dati demo a dati aziendali reali — import clienti, tecnici, storico.
> La versione 1.0.0 coincide con il momento in cui il sistema va in produzione.

---

## 1.x.x · Post go-live

- App mobile semplificata per i tecnici (interventi del giorno, chiusura, firma)
- Fatturazione o integrazione con software contabile
- Ottimizzazione VRP automatica schedulata (notturna)
- Dashboard direzionale con KPI aggregati e trend
