# Progetto Colombini SNC

## Preferenze generali
- Rispondere sempre in italiano, anche dopo compattazioni del contesto.
- Se dei file sono stati dimenticati nell'ultimo commit, usare `git commit --amend --no-edit` invece di un nuovo commit separato (commit atomici e puliti).
- Le preferenze e regole di progetto vanno sempre in questo file `CLAUDE.md` (non nel sistema di memoria), così possono essere pushate e condivise.
- **Review del codice**: creare i file direttamente con Write/Edit e lasciare che l'utente approvi i diff nell'IDE. Non mostrare preview del codice in chat prima di scrivere i file.

## AdminLTE 3 — Layout card
In AdminLTE 3, non usare `d-flex justify-content-between` su `.card-header` e `.card-footer` (il flex annidato non funziona come atteso).

- **Header con pulsante a destra** → usare `.card-tools` (ha `margin-left: auto` integrato):
```html
<div class="card-header">
    <h3 class="card-title">Titolo</h3>
    <div class="card-tools">
        <a href="..." class="btn btn-primary btn-sm">Azione</a>
    </div>
</div>
```
- **Footer con Annulla/Salva** → usare `clearfix` + float:
```html
<div class="card-footer clearfix">
    <a href="..." class="btn btn-secondary float-left">Annulla</a>
    <button type="submit" class="btn btn-primary float-right">Salva</button>
</div>
```

## Changelog
Prima di ogni commit aggiornare `CHANGELOG.md` seguendo il pattern markdown esistente e includerlo nella stessa commit.
Il sistema confronta `CHANGELOG.md` con il campo `users.ultima_versione_vista` per mostrare le novità all'avvio dell'applicazione.

## Dominio aziendale
Il dominio aziendale è **colombini-snc.it**.
Usare per email admin (es. admin@colombini-snc.it), baseURL in produzione e qualsiasi riferimento al dominio.
