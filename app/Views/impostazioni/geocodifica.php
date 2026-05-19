<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item active">Geocodifica Clienti</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">

        <!-- Riepilogo -->
        <div class="row mb-3">
            <div class="col-4">
                <div class="info-box mb-0 shadow-none border">
                    <span class="info-box-icon bg-success"><i class="fas fa-map-marker-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text small">Geocodificati</span>
                        <span class="info-box-number"><?= $geocodati ?> / <?= $totale ?></span>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box mb-0 shadow-none border">
                    <span class="info-box-icon bg-danger"><i class="fas fa-question-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text small">Da geocodificare</span>
                        <span class="info-box-number" id="stat-da-fare"><?= $da_fare ?></span>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box mb-0 shadow-none border">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text small">Falliti</span>
                        <span class="info-box-number" id="stat-falliti"><?= $falliti ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avvia geocodifica -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marked-alt mr-1"></i> Geocodifica massiva</h3>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Il sistema contatterà Nominatim (OpenStreetMap) per ricavare le coordinate di ogni cliente
                    non ancora geocodificato. La procedura avanza un cliente alla volta per rispettare i limiti
                    del servizio.
                </p>

                <!-- Barra progresso (nascosta inizialmente) -->
                <div id="progress-wrap" style="display:none;" class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span id="progress-label">Elaborazione in corso…</span>
                        <span id="progress-counts"></span>
                    </div>
                    <div class="progress" style="height:20px;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                             role="progressbar" style="width:0%">0%</div>
                    </div>
                </div>

                <!-- Log risultati -->
                <div id="log-wrap" style="display:none; max-height:220px; overflow-y:auto;" class="mb-3">
                    <ul id="log-list" class="list-unstyled small mb-0"></ul>
                </div>

                <!-- Messaggio completamento -->
                <div id="msg-done" style="display:none;" class="alert alert-success mb-3">
                    <i class="fas fa-check-circle mr-1"></i> Geocodifica completata.
                    <a href="<?= base_url('impostazioni/geocodifica') ?>" class="alert-link ml-2">Aggiorna i contatori</a>.
                </div>

                <div class="d-flex align-items-center">
                    <button id="btn-avvia" class="btn btn-primary btn-sm mr-2"
                            <?= $da_fare === 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-play mr-1"></i>
                        Avvia<?= $da_fare > 0 ? ' (' . $da_fare . ' clienti)' : '' ?>
                    </button>
                    <button id="btn-riprova" class="btn btn-outline-warning btn-sm mr-2"
                            <?= $falliti === 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-redo mr-1"></i>
                        Riprova falliti<?= $falliti > 0 ? ' (' . $falliti . ')' : '' ?>
                    </button>
                    <button id="btn-stop" class="btn btn-outline-secondary btn-sm" style="display:none;">
                        <i class="fas fa-stop mr-1"></i> Interrompi
                    </button>
                </div>
            </div>
            <div class="card-footer text-muted small">
                <i class="fas fa-info-circle mr-1"></i>
                Powered by <a href="https://nominatim.openstreetmap.org" target="_blank">Nominatim / OpenStreetMap</a> —
                uso conforme alla <a href="https://operations.osmfoundation.org/policies/nominatim/" target="_blank">usage policy</a>.
            </div>
        </div>

        <?php if (! empty($clienti_falliti)): ?>
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-1 text-warning"></i>
                    Indirizzi non trovati
                </h3>
                <div class="card-tools">
                    <span class="badge badge-warning"><?= count($clienti_falliti) ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Cliente</th>
                            <th class="d-none d-md-table-cell">Indirizzo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($clienti_falliti as $c): ?>
                        <tr>
                            <td class="text-muted small align-middle"><?= esc($c['codice']) ?></td>
                            <td class="align-middle">
                                <?php if ($c['tipo'] === 'persona_fisica'): ?>
                                    <?= esc(trim(($c['cognome'] ?? '') . ' ' . ($c['nome'] ?? ''))) ?>
                                <?php else: ?>
                                    <?= esc($c['ragsoc'] ?? '') ?>
                                <?php endif; ?>
                            </td>
                            <td class="d-none d-md-table-cell align-middle text-muted small">
                                <?= esc(trim(($c['indirizzo'] ?? '') . ', ' . ($c['citta'] ?? ''))) ?>
                            </td>
                            <td class="text-right align-middle">
                                <a href="<?= base_url('clienti/' . $c['id'] . '/edit?from=impostazioni/geocodifica') ?>"
                                   class="btn btn-xs btn-outline-warning" title="Correggi indirizzo">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <div class="text-right">
            <a href="<?= base_url('impostazioni') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Torna alle impostazioni
            </a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/impostazioni/geocodifica') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    var running   = false;
    var forceMode = false;
    var processed = 0;
    var total     = parseInt('<?= $da_fare ?>') || 0;
    var afterId   = 0;
    var stepUrl   = '<?= base_url('impostazioni/geocodifica-step') ?>';
    var csrfName  = '<?= csrf_token() ?>';
    var csrfHash  = '<?= csrf_hash() ?>';

    function updateCsrf(name, hash) {
        csrfName = name;
        csrfHash = hash;
    }

    function log(msg, cls) {
        var li = document.createElement('li');
        li.innerHTML = '<span class="text-' + (cls || 'muted') + '">' + msg + '</span>';
        document.getElementById('log-list').prepend(li);
    }

    function setProgress(pct) {
        var bar = document.getElementById('progress-bar');
        bar.style.width = pct + '%';
        bar.textContent = pct + '%';
    }

    function step() {
        if (! running) return;

        var data = new FormData();
        data.append(csrfName, csrfHash);
        if (forceMode) data.append('force', '1');
        if (afterId)   data.append('after_id', afterId);

        fetch(stepUrl, { method: 'POST', body: data })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (json.csrf_token) updateCsrf(json.csrf_token_name || csrfName, json.csrf_token);

                if (json.done) {
                    finish();
                    return;
                }

                if (json.after_id) afterId = json.after_id;
                processed++;
                var icon = json.esito === 'ok'
                    ? '<i class="fas fa-check text-success mr-1"></i>'
                    : '<i class="fas fa-times text-warning mr-1"></i>';
                var cls  = json.esito === 'ok' ? 'success' : 'warning';
                log(icon + esc(json.cliente), cls);

                var pct = total > 0 ? Math.round((processed / total) * 100) : 100;
                setProgress(Math.min(pct, 99));
                document.getElementById('progress-counts').textContent =
                    processed + ' / ' + total;

                setTimeout(step, 300);
            })
            .catch(function () {
                log('<i class="fas fa-exclamation-circle mr-1"></i> Errore di rete — riprova.', 'danger');
                finish();
            });
    }

    function start(force) {
        if (running) return;
        forceMode = force;
        processed = 0;
        total     = force
            ? parseInt(document.getElementById('stat-falliti').textContent) || 0
            : parseInt(document.getElementById('stat-da-fare').textContent)  || 0;

        afterId   = 0;
        running   = true;
        document.getElementById('progress-wrap').style.display = '';
        document.getElementById('log-wrap').style.display = '';
        document.getElementById('msg-done').style.display = 'none';
        document.getElementById('btn-avvia').disabled   = true;
        document.getElementById('btn-riprova').disabled = true;
        document.getElementById('btn-stop').style.display = '';
        setProgress(0);
        step();
    }

    function finish() {
        running = false;
        setProgress(100);
        document.getElementById('progress-bar').classList.remove('progress-bar-animated');
        document.getElementById('progress-label').textContent = 'Completato.';
        document.getElementById('msg-done').style.display = '';
        document.getElementById('btn-stop').style.display  = 'none';
    }

    function esc(s) {
        var d = document.createElement('div');
        d.textContent = s || '';
        return d.innerHTML;
    }

    document.getElementById('btn-avvia').addEventListener('click',   function () { start(false); });
    document.getElementById('btn-riprova').addEventListener('click', function () { start(true);  });
    document.getElementById('btn-stop').addEventListener('click', function () {
        running = false;
        document.getElementById('progress-label').textContent = 'Interrotto.';
        document.getElementById('progress-bar').classList.remove('progress-bar-animated');
        document.getElementById('btn-stop').style.display  = 'none';
        document.getElementById('btn-avvia').disabled   = false;
        document.getElementById('btn-riprova').disabled = false;
    });
})();
</script>
<?= $this->endSection() ?>
