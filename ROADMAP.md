# Roadmap — Colombini SNC Gestionale

*Stato attuale: v0.24.0 — Refactoring controller Impostazioni in subfolder.*
*Ultimo aggiornamento: 2026-06-09*
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
| **Magazzino — prodotti chimici** | Categoria da aggiungere quando disponibile; struttura già pronta |
| **Intervento → consumo materiali** | Nel form di chiusura il tecnico seleziona i materiali usati; scalati dal magazzino |
| **Rapportino PDF — sezione materiali** | Il PDF già generato da Dompdf include i materiali utilizzati nell'intervento |
| **Impianti — censimento piscine** | CRUD impianto per cliente (tipo, anno installazione, caratteristiche); collegato alla scheda cliente |
| **Impianti — trattamento acqua** | Sotto-sezione separata nella sidebar (già strutturata) |

## 0.26 — 0.28 · Visite in abbonamento *(priorità alta — generano interventi programmati)*

| Feature | Note |
|---|---|
| **Scheletro abbonamenti** | Contratti di manutenzione periodica per cliente: frequenza, tipo intervento, periodo validità |
| **Generazione automatica interventi programmati** | Gli abbonamenti generano automaticamente interventi con stato `programmato` alle scadenze previste |
| **Materiali da portare alla visita successiva** | Il tecnico nella chiusura dell'intervento indica i materiali da portare alla visita successiva; salvati con riferimento a cliente e intervento successivo |
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
