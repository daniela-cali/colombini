<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti') ?>">Clienti</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti/' . $cliente['id']) ?>"><?= esc($cliente['ragsoc'] ?? trim(($cliente['cognome'] ?? '') . ' ' . ($cliente['nome'] ?? ''))) ?></a></li>
    <li class="breadcrumb-item active">Modifica</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">

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
                <h3 class="card-title">Modifica Cliente</h3>
            </div>
            <form method="post" action="<?= base_url('clienti/' . $cliente['id']) ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <!-- Tipo cliente -->
                    <div class="form-group">
                        <label>Tipo cliente <span class="text-danger">*</span></label>
                        <div class="d-flex">
                            <div class="tipo-btn mr-3">
                                <input type="radio" name="tipo" id="tipo_societa" value="societa"
                                       class="d-none tipo-radio"
                                       <?= (old('tipo', $cliente['tipo']) === 'societa') ? 'checked' : '' ?>>
                                <label for="tipo_societa"
                                       class="btn btn-outline-primary px-4 tipo-label <?= (old('tipo', $cliente['tipo']) === 'societa') ? 'active' : '' ?>">
                                    <i class="fas fa-building mr-1"></i> Società / Ditta
                                </label>
                            </div>
                            <div class="tipo-btn">
                                <input type="radio" name="tipo" id="tipo_pf" value="persona_fisica"
                                       class="d-none tipo-radio"
                                       <?= (old('tipo', $cliente['tipo']) === 'persona_fisica') ? 'checked' : '' ?>>
                                <label for="tipo_pf"
                                       class="btn btn-outline-primary px-4 tipo-label <?= (old('tipo', $cliente['tipo']) === 'persona_fisica') ? 'active' : '' ?>">
                                    <i class="fas fa-user mr-1"></i> Persona fisica
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Ragione sociale (solo società) -->
                    <div class="form-group" id="field-ragsoc">
                        <label for="ragsoc">Ragione sociale <span class="text-danger">*</span></label>
                        <input type="text" name="ragsoc" id="ragsoc" class="form-control"
                               maxlength="255"
                               value="<?= esc(old('ragsoc', $cliente['ragsoc'])) ?>"
                               placeholder="es. Colombini S.n.c.">
                    </div>

                    <!-- Nome e Cognome (solo persona fisica) -->
                    <div class="form-row" id="field-persona">
                        <div class="form-group col-md-6">
                            <label for="cognome">Cognome <span class="text-danger">*</span></label>
                            <input type="text" name="cognome" id="cognome" class="form-control"
                                   maxlength="100"
                                   value="<?= esc(old('cognome', $cliente['cognome'])) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nome">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control"
                                   maxlength="100"
                                   value="<?= esc(old('nome', $cliente['nome'])) ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="form-row">
                        <!-- Codice -->
                        <div class="form-group col-md-4">
                            <label for="codice">Codice <span class="text-danger">*</span></label>
                            <input type="text" name="codice" id="codice" class="form-control"
                                   maxlength="15"
                                   value="<?= esc(old('codice', $cliente['codice'])) ?>">
                        </div>
                        <!-- P.IVA -->
                        <div class="form-group col-md-4">
                            <label for="piva">P.IVA</label>
                            <input type="text" name="piva" id="piva" class="form-control"
                                   maxlength="15"
                                   value="<?= esc(old('piva', $cliente['piva'])) ?>"
                                   placeholder="es. 01234567890">
                        </div>
                        <!-- Codice Fiscale -->
                        <div class="form-group col-md-4">
                            <label for="cfisc">Codice Fiscale</label>
                            <input type="text" name="cfisc" id="cfisc" class="form-control"
                                   maxlength="16"
                                   value="<?= esc(old('cfisc', $cliente['cfisc'])) ?>"
                                   placeholder="es. RSSMRA80A01H501U"
                                   style="text-transform:uppercase">
                        </div>
                    </div>

                    <hr>

                    <!-- Indirizzo -->
                    <div class="form-group">
                        <label for="indirizzo">Indirizzo</label>
                        <input type="text" name="indirizzo" id="indirizzo" class="form-control"
                               maxlength="255"
                               value="<?= esc(old('indirizzo', $cliente['indirizzo'])) ?>"
                               placeholder="Via, numero civico">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="citta">Città</label>
                            <input type="text" name="citta" id="citta" class="form-control"
                                   maxlength="100"
                                   value="<?= esc(old('citta', $cliente['citta'])) ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="cap">CAP</label>
                            <input type="text" name="cap" id="cap" class="form-control"
                                   maxlength="10"
                                   value="<?= esc(old('cap', $cliente['cap'])) ?>"
                                   placeholder="es. 20100">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="provincia">Provincia</label>
                            <input type="text" name="provincia" id="provincia" class="form-control"
                                   maxlength="5"
                                   value="<?= esc(old('provincia', $cliente['provincia'])) ?>"
                                   placeholder="es. MI">
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-geo"
                                onclick="verificaIndirizzo()">
                            <i class="fas fa-map-marker-alt mr-1"></i> Verifica indirizzo
                        </button>
                        <span id="geo-result" class="ml-2 small">
                            <?php if ($cliente['geocoded_at']): ?>
                                <span class="text-success">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <?= number_format((float)$cliente['lat'], 6) ?>, <?= number_format((float)$cliente['lng'], 6) ?>
                                    <span class="text-muted">(<?= date('d/m/Y', strtotime($cliente['geocoded_at'])) ?>)</span>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <hr>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="telefono">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control"
                                   maxlength="50"
                                   value="<?= esc(old('telefono', $cliente['telefono'])) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   maxlength="255"
                                   value="<?= esc(old('email', $cliente['email'])) ?>">
                        </div>
                    </div>

                    <!-- Note -->
                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea name="note" id="note" class="form-control" rows="3"
                                  maxlength="3000"><?= esc(old('note', $cliente['note'])) ?></textarea>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('clienti/' . $cliente['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salva modifiche
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    function aggiornaTipo() {
        var val = document.querySelector('.tipo-radio:checked')?.value ?? 'societa';

        document.querySelectorAll('.tipo-label').forEach(function (lbl) {
            lbl.classList.remove('active');
        });
        var checked = document.querySelector('.tipo-radio:checked');
        if (checked) {
            checked.nextElementSibling.classList.add('active');
        }

        if (val === 'persona_fisica') {
            document.getElementById('field-ragsoc').style.display  = 'none';
            document.getElementById('field-persona').style.display = '';
        } else {
            document.getElementById('field-ragsoc').style.display  = '';
            document.getElementById('field-persona').style.display = 'none';
        }
    }

    document.querySelectorAll('.tipo-radio').forEach(function (r) {
        r.addEventListener('change', aggiornaTipo);
    });

    aggiornaTipo();
})();

function verificaIndirizzo() {
    var indirizzo = document.getElementById('indirizzo').value.trim();
    var citta     = document.getElementById('citta').value.trim();
    var cap       = document.getElementById('cap').value.trim();
    var result    = document.getElementById('geo-result');
    var btn       = document.getElementById('btn-geo');

    if (!indirizzo && !citta) {
        result.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Inserisci almeno indirizzo o città.</span>';
        return;
    }

    btn.disabled = true;
    result.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Ricerca in corso…';

    var q = [indirizzo, cap, citta, 'Italia'].filter(Boolean).join(', ');
    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q);

    fetch(url, { headers: { 'Accept-Language': 'it' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data && data[0]) {
                var lat = parseFloat(data[0].lat).toFixed(6);
                var lng = parseFloat(data[0].lon).toFixed(6);
                result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i>' + lat + ', ' + lng + '</span>';
            } else {
                result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Indirizzo non trovato — verifica i dati.</span>';
            }
        })
        .catch(function() {
            result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Errore di rete.</span>';
        })
        .finally(function() { btn.disabled = false; });
}
</script>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<h5><i class="fas fa-user-edit mr-1"></i> Modifica dati cliente</h5>
<p>Tutti i campi sono modificabili tranne il codice cliente, che può essere cambiato con cautela poiché è usato come riferimento negli interventi collegati.</p>
<h5><i class="fas fa-map-marker-alt mr-1"></i> Aggiornamento indirizzo</h5>
<p>Se modifichi indirizzo o città, clicca <strong>Verifica indirizzo</strong> per aggiornare le coordinate. La data di geocodifica mostrata indica l'ultimo rilevamento salvato.</p>
<h5><i class="fas fa-sticky-note mr-1"></i> Note</h5>
<p>Le note sono visibili solo agli operatori interni. Usale per annotare informazioni utili come preferenze di orario, accessi, contatti alternativi.</p>
<p><span class="badge-tip"><i class="fas fa-lightbulb mr-1"></i> Per rieseguire la geocodifica di tutti i clienti in batch usa <code>php spark geocode:clienti</code> da terminale.</span></p>
<?= $this->endSection() ?>