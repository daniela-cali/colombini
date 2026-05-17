<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('interventi') ?>">Interventi</a></li>
    <li class="breadcrumb-item active">Nuovo Intervento</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">

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

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Nuovo Intervento</h3>
            </div>
            <form method="post" action="<?= base_url('interventi') ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <!-- Richiesta portale collegata -->
                    <?php if (!empty($richieste)): ?>
                    <div class="form-group">
                        <label>Richiesta portale collegata
                            <small class="text-muted font-weight-normal">(opzionale — precompila i campi)</small>
                        </label>
                        <select name="richiesta_id" id="richiesta_id" class="form-control">
                            <option value="">— Nessuna richiesta collegata —</option>
                            <?php foreach ($richieste as $r): ?>
                                <option value="<?= $r['id'] ?>"
                                    data-tipo="<?= esc($r['tipo_intervento']) ?>"
                                    data-luogo="<?= esc($r['luogo_intervento'] ?? '') ?>"
                                    data-cliente="<?= $r['cliente_id_resolved'] ?? '' ?>"
                                    data-note="<?= esc($r['note']) ?>"
                                    <?= (old('richiesta_id', $richiesta_id_pre) == $r['id']) ? 'selected' : '' ?>>
                                    #<?= $r['id'] ?> — <?= esc($r['richiedente']) ?>
                                    (<?= esc($r['tipo_intervento']) ?>)
                                    — <?= date('d/m/Y', strtotime($r['created_at'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <!-- Tipo intervento -->
                        <div class="form-group col-md-6">
                            <label>Tipo di intervento <span class="text-danger">*</span></label>
                            <select name="tipo_intervento" id="tipo_intervento" class="form-control" required>
                                <option value="">— Seleziona —</option>
                                <?php foreach ($tipi as $key => $label): ?>
                                    <option value="<?= $key ?>"
                                        <?= old('tipo_intervento') === $key ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Stato -->
                        <div class="form-group col-md-6">
                            <label>Stato</label>
                            <select name="stato" class="form-control">
                                <option value="pianificato" selected>Pianificato</option>
                            </select>
                        </div>
                    </div>

                    <!-- Luogo intervento + geo -->
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label>Luogo <small class="text-muted font-weight-normal">(descrizione)</small></label>
                            <input type="text" name="luogo_intervento" id="luogo_intervento"
                                   class="form-control" value="<?= esc(old('luogo_intervento')) ?>"
                                   placeholder="es. Stabilimento Rossi">
                        </div>
                        <div class="form-group col-md-5">
                            <label>Città / Indirizzo <small class="text-muted font-weight-normal">(per geolocalizzazione)</small></label>
                            <input type="text" name="citta" id="citta"
                                   class="form-control" value="<?= esc(old('citta')) ?>"
                                   placeholder="es. Via Roma 10, Bergamo">
                        </div>
                        <div class="form-group col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary btn-block" id="btn-geo"
                                    title="Rileva coordinate">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <span class="d-none d-lg-inline">Geo</span>
                            </button>
                        </div>
                    </div>
                    <div id="geo-result" class="small mb-2" style="margin-top:-8px;"></div>
                    <input type="hidden" name="lat" id="lat" value="<?= esc(old('lat')) ?>">
                    <input type="hidden" name="lng" id="lng" value="<?= esc(old('lng')) ?>">

                    <div class="form-row">
                        <!-- Cliente -->
                        <div class="form-group col-md-6">
                            <label>Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="form-control">
                                <option value="">— Nessuno —</option>
                                <?php foreach ($clienti as $c): ?>
                                    <option value="<?= $c['id'] ?>"
                                        <?= old('cliente_id') == $c['id'] ? 'selected' : '' ?>>
                                        <?= esc($c['tipo'] === 'persona_fisica'
                                            ? trim(($c['cognome'] ?? '') . ' ' . ($c['nome'] ?? ''))
                                            : ($c['ragsoc'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tecnico -->
                        <div class="form-group col-md-6">
                            <label>Tecnico assegnato</label>
                            <select name="tecnico_id" class="form-control">
                                <option value="">— Non assegnato —</option>
                                <?php foreach ($tecnici as $t): ?>
                                    <option value="<?= $t->id ?>"
                                        <?= (old('tecnico_id') == $t->id || $tecnico_id_pre == $t->id) ? 'selected' : '' ?>>
                                        <?= esc($t->cognome . ' ' . $t->nome) ?>
                                        <?= $t->telefono ? '(' . esc($t->telefono) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Data pianificata -->
                    <div class="form-group">
                        <label>Data e ora pianificata</label>
                        <input type="datetime-local" name="data_pianificata" class="form-control"
                               value="<?= esc(old('data_pianificata')) ?>">
                    </div>

                    <!-- Descrizione -->
                    <div class="form-group">
                        <label>Descrizione / Problema</label>
                        <textarea name="descrizione" id="descrizione" class="form-control" rows="4"
                                  placeholder="Descrizione del lavoro da eseguire..."
                                  maxlength="3000"><?= esc(old('descrizione')) ?></textarea>
                    </div>

                    <!-- Note interne -->
                    <div class="form-group">
                        <label>Note interne <small class="text-muted">(non visibili al cliente)</small></label>
                        <textarea name="note_interne" class="form-control" rows="2"
                                  maxlength="3000"><?= esc(old('note_interne')) ?></textarea>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('interventi') ?>" class="btn btn-secondary float-left">
                        <i class="fas fa-arrow-left mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-save mr-1"></i> Crea Intervento
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var richiestaSel = document.getElementById('richiesta_id');
if (richiestaSel) {
    function precompila(sel) {
        var opt = sel.options[sel.selectedIndex];
        if (!opt.value) return;
        var tipo    = document.getElementById('tipo_intervento');
        var luogo   = document.getElementById('luogo_intervento');
        var cliente = document.getElementById('cliente_id');
        var descr   = document.getElementById('descrizione');
        if (opt.dataset.tipo)    tipo.value    = opt.dataset.tipo;
        if (opt.dataset.luogo)   luogo.value   = opt.dataset.luogo;
        if (opt.dataset.cliente) cliente.value = opt.dataset.cliente;
        if (opt.dataset.note)    descr.value   = opt.dataset.note;
    }
    richiestaSel.addEventListener('change', function () { precompila(this); });
    // Pre-compila subito se arrivato da link con richiesta_id
    if (richiestaSel.value) precompila(richiestaSel);
}

document.getElementById('btn-geo').addEventListener('click', function () {
    var citta   = document.getElementById('citta').value.trim();
    var luogo   = document.getElementById('luogo_intervento').value.trim();
    var query   = citta || luogo;
    var result  = document.getElementById('geo-result');
    var btn     = this;

    if (!query) {
        result.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Inserisci città o indirizzo prima.</span>';
        return;
    }

    btn.disabled = true;
    result.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Geolocalizzazione in corso…';

    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q='
            + encodeURIComponent(query + ', Italia');

    fetch(url, { headers: { 'Accept-Language': 'it' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data && data[0]) {
                var lat = parseFloat(data[0].lat).toFixed(6);
                var lng = parseFloat(data[0].lon).toFixed(6);
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;
                result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i>'
                    + lat + ', ' + lng
                    + ' <span class="text-muted">(' + data[0].display_name.split(',').slice(0,2).join(',') + ')</span></span>';
            } else {
                result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Indirizzo non trovato.</span>';
            }
        })
        .catch(function () {
            result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Errore di rete.</span>';
        })
        .finally(function () { btn.disabled = false; });
});
</script>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/interventi/create') ?>
<?= $this->endSection() ?>
