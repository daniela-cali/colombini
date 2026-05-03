<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item active">Parametri Generali</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form method="post" action="<?= base_url('impostazioni/parametri') ?>">
    <?= csrf_field() ?>
    <div class="row">

        <!-- Sede aziendale -->
        <div class="col-lg-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building mr-1"></i> Sede aziendale</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Usata come punto di partenza per il calcolo dei tempi di viaggio.
                    </p>
                    <div class="form-group">
                        <label>Nome / Ragione sociale</label>
                        <input type="text" name="sede_nome" class="form-control"
                               value="<?= esc(setting('Azienda.sede_nome') ?? '') ?>"
                               placeholder="es. Colombini Piscine S.n.c.">
                    </div>
                    <div class="form-group">
                        <label>Indirizzo</label>
                        <input type="text" name="sede_indirizzo" class="form-control"
                               value="<?= esc(setting('Azienda.sede_indirizzo') ?? '') ?>"
                               placeholder="es. Via Roma 1">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-4">
                            <label>CAP</label>
                            <input type="text" name="sede_cap" class="form-control"
                                   value="<?= esc(setting('Azienda.sede_cap') ?? '') ?>"
                                   placeholder="00000" maxlength="5">
                        </div>
                        <div class="form-group col-8">
                            <label>Città</label>
                            <input type="text" name="sede_citta" class="form-control"
                                   value="<?= esc(setting('Azienda.sede_citta') ?? '') ?>"
                                   placeholder="es. Milano">
                        </div>
                    </div>
                    <div class="form-row align-items-end">
                        <div class="form-group col-5">
                            <label>Latitudine</label>
                            <input type="number" name="sede_lat" id="sede_lat" class="form-control"
                                   step="0.000001" min="-90" max="90"
                                   value="<?= esc(setting('Azienda.sede_lat') ?? '') ?>"
                                   placeholder="es. 45.464664">
                        </div>
                        <div class="form-group col-5">
                            <label>Longitudine</label>
                            <input type="number" name="sede_lng" id="sede_lng" class="form-control"
                                   step="0.000001" min="-180" max="180"
                                   value="<?= esc(setting('Azienda.sede_lng') ?? '') ?>"
                                   placeholder="es. 9.186523">
                        </div>
                        <div class="form-group col-2">
                            <button type="button" class="btn btn-outline-secondary btn-block" id="btn-geo-sede"
                                    onclick="geocodificaSede()" title="Rileva coordinate dall'indirizzo">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div id="geo-sede-result" class="small mb-2"></div>
                </div>
            </div>
        </div>

        <!-- Orari default tecnici -->
        <div class="col-lg-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Orari default tecnici</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Valori precompilati quando si configura un nuovo tecnico.
                    </p>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label>Inizio giornata</label>
                            <input type="time" name="orario_inizio" class="form-control"
                                   value="<?= esc(setting('Tecnici.orario_inizio') ?? '08:00') ?>">
                        </div>
                        <div class="form-group col-6">
                            <label>Fine giornata</label>
                            <input type="time" name="orario_fine" class="form-control"
                                   value="<?= esc(setting('Tecnici.orario_fine') ?? '17:00') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label>Pausa pranzo — dalle</label>
                            <input type="time" name="pausa_inizio" class="form-control"
                                   value="<?= esc(setting('Tecnici.pausa_inizio') ?? '13:00') ?>">
                        </div>
                        <div class="form-group col-6">
                            <label>Pausa pranzo — alle</label>
                            <input type="time" name="pausa_fine" class="form-control"
                                   value="<?= esc(setting('Tecnici.pausa_fine') ?? '14:00') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Durate interventi -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-stopwatch mr-1"></i> Durata standard interventi</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Minuti stimati per ogni tipo di intervento, usati nella pianificazione.
                    </p>
                    <?php foreach ($tipi as $key => $label): ?>
                        <div class="form-group row align-items-center mb-2">
                            <label class="col-6 col-form-label"><?= esc($label) ?></label>
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="durata_<?= $key ?>" class="form-control"
                                           min="5" max="480" step="5"
                                           value="<?= (int) (setting('Interventi.durata_' . $key) ?? $durate[$key]) ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 text-muted small">
                                <?= gmdate('G\h i\'', ($durate[$key]) * 60) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12 text-right mb-3">
            <a href="<?= base_url('impostazioni') ?>" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Indietro
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Salva impostazioni
            </button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function geocodificaSede() {
    var indirizzo = document.querySelector('[name="sede_indirizzo"]').value.trim();
    var citta     = document.querySelector('[name="sede_citta"]').value.trim();
    var cap       = document.querySelector('[name="sede_cap"]').value.trim();
    var result    = document.getElementById('geo-sede-result');
    var btn       = document.getElementById('btn-geo-sede');

    if (!indirizzo && !citta) {
        result.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Inserisci indirizzo o città prima.</span>';
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
                document.getElementById('sede_lat').value = lat;
                document.getElementById('sede_lng').value = lng;
                result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i>Coordinate aggiornate: ' + lat + ', ' + lng + '</span>';
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
