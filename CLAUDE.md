# Progetto Colombini SNC

## Preferenze generali
- Rispondere sempre in italiano, anche dopo compattazioni del contesto.
- Se dei file sono stati dimenticati nell'ultimo commit, usare `git commit --amend --no-edit` invece di un nuovo commit separato (commit atomici e puliti).
- Le preferenze e regole di progetto vanno sempre in questo file `CLAUDE.md` (non nel sistema di memoria), così possono essere pushate e condivise.
- **Review del codice**: creare i file direttamente con Write/Edit e lasciare che l'utente approvi i diff nell'IDE. Non mostrare l'intero file o blocchi lunghi di codice in chat — la spiegazione descrive le modifiche a parole, non ripete il codice verbatim.
- **Spiegazioni passo per passo**: prima di ogni modifica non banale, spiegare passo per passo e riga per riga cosa si sta per fare e perché, come farebbe un insegnante — cosa cambia, perché si sceglie quell'approccio, quali effetti produce. Solo dopo usare Write/Edit. Eccezione: per modifiche di una sola riga o correzioni ovvie basta una frase di contesto.
- **Branch Git**: non aprire un branch per ogni piccola modifica. Suggerire attivamente quando NON serve un branch (es. modifiche contenute su una o due view/controller). Usare un branch solo per feature significative o rischiose.

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

## View Help
Per ogni nuova view creare sempre il corrispondente file help in `app/Views/help/<sezione>/<nome_view>.php`, seguendo il pattern delle help esistenti: titoletti `<h5>` con icona FontAwesome, paragrafi `<p>`, eventuale `.badge-tip` per suggerimenti. La view principale include il file con `<?= $this->include('help/<sezione>/<nome_view>') ?>` dentro la sezione `help`.

## Changelog
Prima di ogni commit aggiornare `CHANGELOG.md` seguendo il pattern markdown esistente e includerlo nella stessa commit.
Il sistema confronta `CHANGELOG.md` con il campo `users.ultima_versione_vista` per mostrare le novità all'avvio dell'applicazione.

## Ripristino diff VSCode (Claude Code)
Se il diff delle modifiche smette di apparire nell'IDE VSCode:
1. Verificare che `~/.claude/settings.json` abbia `"defaultMode": "default"` (non `"acceptEdits"`)
2. Eseguire `Ctrl+Shift+P` → **Developer: Reload Window** per ricaricare l'estensione
3. Se non basta, aprire una nuova sessione di Claude Code

## Query database — CodeIgniter 4
Usare sempre il **Query Builder** di CI4 (`$db->table(...)` o il model builder). Evitare query raw (`$db->query(...)`) anche per JOIN complessi: usare la stringa di condizione nel terzo parametro di `->join()` con `$db->escape()` per i valori dinamici.

```php
// JOIN con condizioni multiple — Query Builder
$db->table('users u')
   ->join('tecnici_competenze tc',
          'tc.tecnico_id = u.id AND tc.tipo_intervento_id = ' . $tipoId . ' AND tc.livello >= 1',
          'inner')
   ->join('interventi i',
          'i.tecnico_id = u.id AND DATE(i.data_pianificata) = ' . $db->escape($data),
          'left');
```

## Commenti sui metodi PHP
Aggiungere sempre un commento descrittivo sopra ogni metodo di controller o model, su una o più righe, che spieghi **cosa fa e perché** (non solo il nome). Usare `//` senza docblock formale:

```php
// Restituisce il tecnico referente meno occupato per il tipo dato,
// escludendo chi supera la soglia di interventi giornalieri.
public function tecnicoReferente(...): ?array { ... }
```

## Dominio aziendale
Il dominio aziendale è **colombini-snc.it**.
Usare per email admin (es. admin@colombini-snc.it), baseURL in produzione e qualsiasi riferimento al dominio.
