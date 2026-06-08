<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('magazzino') ?>">Magazzino</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('magazzino/movimenti') ?>">Movimenti</a></li>
    <li class="breadcrumb-item active">Nuovo Movimento</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Nuovo Movimento</h3>
    </div>
    <form method="post" action="<?= base_url('magazzino/movimenti') ?>" id="form-movimento">
        <?= csrf_field() ?>
        <div class="card-body">

            <!-- Riga intestazione -->
            <div class="form-row align-items-end">
                <div class="form-group col-md-3">
                    <label for="tipo">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo" id="tipo" class="form-control">
                        <?php foreach ($tipi as $k => $v): ?>
                            <option value="<?= $k ?>" <?= old('tipo', 'carico') === $k ? 'selected' : '' ?>>
                                <?= esc($v) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="data">Data <span class="text-danger">*</span></label>
                    <input type="date" name="data" id="data" class="form-control"
                           value="<?= old('data', date('Y-m-d')) ?>">
                </div>
                <div class="form-group col-md-3" id="field-num-doc">
                    <label for="num_documento">N° documento</label>
                    <input type="text" name="num_documento" id="num_documento" class="form-control"
                           maxlength="50" placeholder="es. DDT-2024/001"
                           value="<?= esc(old('num_documento')) ?>">
                </div>
                <div class="form-group col-md-4" id="field-fornitore">
                    <label for="fornitore_id">Fornitore</label>
                    <select name="fornitore_id" id="fornitore_id" class="form-control">
                        <option value="">— nessuno / sconosciuto —</option>
                        <?php foreach ($fornitori as $fid => $fnome): ?>
                            <option value="<?= $fid ?>" <?= old('fornitore_id') == $fid ? 'selected' : '' ?>>
                                <?= esc($fnome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="note">Note</label>
                <input type="text" name="note" id="note" class="form-control"
                       maxlength="500" value="<?= esc(old('note')) ?>">
            </div>

            <hr>

            <!-- Tabella righe articoli -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0"><i class="fas fa-list mr-1"></i> Articoli</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-add-riga">
                    <i class="fas fa-plus mr-1"></i> Aggiungi riga
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="tabella-righe">
                    <thead class="thead-light">
                        <tr>
                            <th style="min-width:160px;width:30%">Articolo <span class="text-danger">*</span></th>
                            <th style="width:80px">Qtà <span class="text-danger">*</span></th>
                            <th style="width:95px" class="col-costo">Listino (€)</th>
                            <th style="width:55px" class="col-costo">Sc.1%</th>
                            <th style="width:55px" class="col-costo">Sc.2%</th>
                            <th style="width:55px" class="col-costo">Sc.3%</th>
                            <th style="width:100px" class="col-costo">Costo netto (€)</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="righe-container">
                        <!-- Prima riga inserita via JS al caricamento -->
                    </tbody>
                </table>
            </div>

        </div>
        <div class="card-footer clearfix">
            <a href="<?= base_url('magazzino/movimenti') ?>" class="btn btn-secondary float-left">
                <i class="fas fa-times mr-1"></i> Annulla
            </a>
            <button type="submit" class="btn btn-primary float-right">
                <i class="fas fa-save mr-1"></i> Registra movimento
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/magazzino/movimenti_create') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    // Dati articoli dal server: [{id, cod_articolo, descrizione, fornitore_id}, ...]
    const ARTICOLI = <?= json_encode(array_values($articoli)) ?>;

    const TIPI_CON_COSTO = ['carico', 'inventario'];
    const TIPI_CON_FORNITORE = ['carico'];

    let rigaIdx = 0;

    // ── Costruisce le <option> per un select-articolo ──────────────────────
    function buildOptions(sel, fid) {
        const currentVal = sel.dataset.lastVal || sel.value || '';

        // Dividi articoli in "del fornitore" e "altri"
        const match  = fid ? ARTICOLI.filter(a => String(a.fornitore_id) === String(fid)) : [];
        const others = fid ? ARTICOLI.filter(a => String(a.fornitore_id) !== String(fid)) : ARTICOLI;

        sel.innerHTML = '<option value="">— seleziona articolo —</option>';

        if (fid && match.length) {
            const grp = document.createElement('optgroup');
            grp.label = 'Articoli di questo fornitore';
            match.forEach(a => grp.appendChild(makeOption(a)));
            sel.appendChild(grp);
        }

        if (others.length) {
            const grp = document.createElement('optgroup');
            grp.label = fid && match.length ? 'Altri articoli' : 'Tutti gli articoli';
            others.forEach(a => grp.appendChild(makeOption(a)));
            sel.appendChild(grp);
        }

        // Ripristina la selezione precedente se ancora valida
        if (currentVal) sel.value = currentVal;
    }

    function makeOption(a) {
        const o = document.createElement('option');
        o.value = a.id;
        o.textContent = '[' + a.cod_articolo + '] ' + a.descrizione;
        o.dataset.fid      = a.fornitore_id   || '';
        o.dataset.listino  = a.prezzo_acquisto || 0;
        o.dataset.sconto1  = a.perc_sconto_1  || 0;
        o.dataset.sconto2  = a.perc_sconto_2  || 0;
        o.dataset.sconto3  = a.perc_sconto_3  || 0;
        return o;
    }

    // Ricalcola costo netto da listino e sconti della riga.
    function ricalcolaNetto(inpListino, inpS1, inpS2, inpS3, inpCosto) {
        const listino = parseFloat(inpListino.value) || 0;
        const s1 = parseFloat(inpS1.value) || 0;
        const s2 = parseFloat(inpS2.value) || 0;
        const s3 = parseFloat(inpS3.value) || 0;
        inpCosto.value = (listino * (1 - s1/100) * (1 - s2/100) * (1 - s3/100)).toFixed(2);
    }

    // ── Aggiorna tutti i select-articolo in base al fornitore corrente ──────
    function aggiornaTuttiSelect() {
        const fid = document.getElementById('fornitore_id').value;
        document.querySelectorAll('.art-select').forEach(sel => {
            sel.dataset.lastVal = sel.value;
            buildOptions(sel, fid);
        });
    }

    // ── Mostra/nasconde colonna costo e campi fornitore/documento ───────────
    function aggiornaTipo() {
        const tipo = document.getElementById('tipo').value;
        const hasCosto    = TIPI_CON_COSTO.includes(tipo);
        const hasFornitore = TIPI_CON_FORNITORE.includes(tipo);

        document.getElementById('field-fornitore').style.display = hasFornitore ? '' : 'none';
        document.getElementById('field-num-doc').style.display   = hasFornitore ? '' : 'none';

        document.querySelectorAll('.col-costo').forEach(el => {
            el.style.display = hasCosto ? '' : 'none';
        });

        if (hasFornitore) {
            aggiornaTuttiSelect();
        } else {
            // Nessun filtro fornitore per scarico/rettifica
            document.querySelectorAll('.art-select').forEach(sel => {
                sel.dataset.lastVal = sel.value;
                buildOptions(sel, null);
            });
        }
    }

    // ── Crea una nuova riga ──────────────────────────────────────────────────
    function addRiga(valArt, valQty, valCosto) {
        const idx  = rigaIdx++;
        const tipo = document.getElementById('tipo').value;
        const fid  = document.getElementById('fornitore_id').value;
        const hasCosto = TIPI_CON_COSTO.includes(tipo);

        const tr = document.createElement('tr');
        tr.dataset.idx = idx;

        // Cella articolo
        const tdArt = document.createElement('td');
        const sel = document.createElement('select');
        sel.name = 'righe[' + idx + '][articolo_id]';
        sel.className = 'form-control form-control-sm art-select';
        buildOptions(sel, TIPI_CON_FORNITORE.includes(tipo) ? fid : null);
        if (valArt) sel.value = valArt;
        tdArt.appendChild(sel);
        tr.appendChild(tdArt);

        // Cella quantità
        const tdQty = document.createElement('td');
        const inpQty = document.createElement('input');
        inpQty.type = 'number';
        inpQty.name = 'righe[' + idx + '][quantita]';
        inpQty.className = 'form-control form-control-sm';
        inpQty.min = 1; inpQty.step = 1;
        inpQty.value = valQty || ''; inpQty.placeholder = '0';
        tdQty.appendChild(inpQty);
        tr.appendChild(tdQty);

        // Helper: crea cella input numerica col-costo
        function cellaCosto(placeholder, step) {
            const td  = document.createElement('td');
            const inp = document.createElement('input');
            td.className = 'col-costo';
            td.style.display = hasCosto ? '' : 'none';
            inp.type = 'number'; inp.className = 'form-control form-control-sm';
            inp.min = 0; inp.step = step || '0.01'; inp.placeholder = placeholder;
            td.appendChild(inp);
            return { td, inp };
        }

        const { td: tdListino, inp: inpListino } = cellaCosto('0.00', '0.01');
        const { td: tdS1,      inp: inpS1      } = cellaCosto('0', '0.01');
        const { td: tdS2,      inp: inpS2      } = cellaCosto('0', '0.01');
        const { td: tdS3,      inp: inpS3      } = cellaCosto('0', '0.01');

        // Costo netto — campo effettivamente salvato
        const tdCosto = document.createElement('td');
        tdCosto.className = 'col-costo';
        tdCosto.style.display = hasCosto ? '' : 'none';
        const inpCosto = document.createElement('input');
        inpCosto.type = 'number';
        inpCosto.name = 'righe[' + idx + '][costo_unitario]';
        inpCosto.className = 'form-control form-control-sm';
        inpCosto.min = 0; inpCosto.step = '0.01'; inpCosto.placeholder = '0.00';
        inpCosto.value = valCosto || '';
        tdCosto.appendChild(inpCosto);

        // Auto-fill listino+sconti quando si sceglie l'articolo
        sel.addEventListener('change', function () {
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.value) return;
            inpListino.value = parseFloat(opt.dataset.listino) > 0 ? parseFloat(opt.dataset.listino).toFixed(2) : '';
            inpS1.value = parseFloat(opt.dataset.sconto1) > 0 ? opt.dataset.sconto1 : '';
            inpS2.value = parseFloat(opt.dataset.sconto2) > 0 ? opt.dataset.sconto2 : '';
            inpS3.value = parseFloat(opt.dataset.sconto3) > 0 ? opt.dataset.sconto3 : '';
            ricalcolaNetto(inpListino, inpS1, inpS2, inpS3, inpCosto);
        });

        // Ricalcola netto quando cambiano listino o sconti
        [inpListino, inpS1, inpS2, inpS3].forEach(function(inp) {
            inp.addEventListener('input', function () {
                ricalcolaNetto(inpListino, inpS1, inpS2, inpS3, inpCosto);
            });
        });

        tr.appendChild(tdListino);
        tr.appendChild(tdS1);
        tr.appendChild(tdS2);
        tr.appendChild(tdS3);
        tr.appendChild(tdCosto);

        // Cella rimuovi
        const tdRm = document.createElement('td');
        tdRm.className = 'text-center';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-sm btn-outline-danger';
        btn.title = 'Rimuovi riga';
        btn.innerHTML = '<i class="fas fa-times"></i>';
        btn.addEventListener('click', function () {
            const rows = document.querySelectorAll('#righe-container tr');
            if (rows.length > 1) {
                tr.remove();
            } else {
                sel.value = ''; inpQty.value = '';
                inpListino.value = ''; inpS1.value = ''; inpS2.value = ''; inpS3.value = '';
                inpCosto.value = '';
            }
        });
        tdRm.appendChild(btn);
        tr.appendChild(tdRm);

        document.getElementById('righe-container').appendChild(tr);
    }

    // ── Event listeners ──────────────────────────────────────────────────────
    document.getElementById('tipo').addEventListener('change', aggiornaTipo);
    document.getElementById('fornitore_id').addEventListener('change', aggiornaTuttiSelect);
    document.getElementById('btn-add-riga').addEventListener('click', function () { addRiga(); });

    // ── Inizializzazione ─────────────────────────────────────────────────────
    aggiornaTipo();  // applica stato iniziale (carico di default)
    addRiga();       // prima riga vuota

    <?php if (old('righe')): ?>
    // Ripopola le righe se il form è stato rimandato indietro dopo errore
    document.getElementById('righe-container').innerHTML = '';
    rigaIdx = 0;
    <?php foreach (old('righe', []) as $r): ?>
    addRiga(<?= (int) ($r['articolo_id'] ?? 0) ?>, <?= (int) ($r['quantita'] ?? 0) ?>, <?= (float) ($r['costo_unitario'] ?? 0) ?>);
    <?php endforeach; ?>
    <?php endif; ?>

})();
</script>
<?= $this->endSection() ?>
