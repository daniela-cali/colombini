<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <?php if (service('request')->getGet('from') === 'calendario'): ?>
        <li class="breadcrumb-item"><a href="<?= base_url('calendario') ?>">Calendario</a></li>
    <?php else: ?>
        <li class="breadcrumb-item"><a href="<?= base_url('interventi') ?>">Interventi</a></li>
    <?php endif; ?>
    <li class="breadcrumb-item active">#<?= $intervento['id'] ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Intervento #<?= $intervento['id'] ?></h3>
                <div class="card-tools">
                    <?php $s = $stati[$intervento['stato']] ?? ['label' => $intervento['stato'], 'badge' => 'badge-secondary']; ?>
                    <span class="badge badge-light text-dark px-3 py-2"><?= $s['label'] ?></span>
                    <?php $from = service('request')->getGet('from'); if ($from): ?>
                    <a href="<?= base_url($from) ?>" class="btn btn-sm btn-outline-secondary ml-2" title="Torna a <?= esc($from) ?>">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Tipo intervento</div>
                    <div class="col-sm-8"><?= esc($tipi[$intervento['tipo_intervento']] ?? $intervento['tipo_intervento']) ?></div>
                </div>

                <?php if ($intervento['luogo_intervento']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Luogo</div>
                    <div class="col-sm-8"><?= esc($intervento['luogo_intervento']) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['citta']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Città / Indirizzo</div>
                    <div class="col-sm-8">
                        <?= esc($intervento['citta']) ?>
                        <?php if ($intervento['geocoded_at']): ?>
                            <br><small class="text-success">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?= number_format((float)$intervento['lat'], 5) ?>,
                                <?= number_format((float)$intervento['lng'], 5) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Cliente</div>
                    <div class="col-sm-8">
                        <?= $intervento['cliente_ragsoc']
                            ? esc($intervento['cliente_ragsoc'])
                            : ($intervento['cliente_nome']
                                ? esc($intervento['cliente_cognome'] . ' ' . $intervento['cliente_nome'])
                                : '<span class="text-muted">—</span>') ?>
                    </div>
                </div>

                <?php
                    $nomeCliente = $intervento['cliente_ragsoc']
                        ?: trim(($intervento['cliente_cognome'] ?? '') . ' ' . ($intervento['cliente_nome'] ?? ''));
                    $richiedente = $intervento['richiesta_richiedente'] ?? '';
                    $mostraRichiedente = $richiedente && $richiedente !== $nomeCliente;
                ?>
                <?php if ($mostraRichiedente): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Richiedente</div>
                    <div class="col-sm-8"><?= esc($richiedente) ?></div>
                </div>
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Tecnico</div>
                    <div class="col-sm-8">
                        <?php if ($intervento['tecnico_nome']): ?>
                            <?= esc($intervento['tecnico_cognome'] . ' ' . $intervento['tecnico_nome']) ?>
                            <?php if ($intervento['tecnico_telefono']): ?>
                                <br><small class="text-muted">
                                    <i class="fas fa-phone mr-1"></i><?= esc($intervento['tecnico_telefono']) ?>
                                </small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">Non assegnato</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Data pianificata</div>
                    <div class="col-sm-8">
                        <?= $intervento['data_pianificata']
                            ? date('d/m/Y \a\l\l\e H:i', strtotime($intervento['data_pianificata']))
                            : '<span class="text-muted">—</span>' ?>
                    </div>
                </div>

                <?php if ($intervento['data_completamento']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Completato il</div>
                    <div class="col-sm-8">
                        <?= date('d/m/Y \a\l\l\e H:i', strtotime($intervento['data_completamento'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['descrizione']): ?>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Descrizione</div>
                    <div class="col-sm-8">
                        <p class="mb-0" style="white-space:pre-wrap"><?= esc($intervento['descrizione']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['note_interne']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Note interne</div>
                    <div class="col-sm-8">
                        <p class="mb-0 text-muted" style="white-space:pre-wrap"><?= esc($intervento['note_interne']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['note_chiusura']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Note di chiusura</div>
                    <div class="col-sm-8">
                        <p class="mb-0" style="white-space:pre-wrap"><?= esc($intervento['note_chiusura']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['richiesta_id']): ?>
                <hr>
                <div class="row">
                    <div class="col-sm-4 text-muted small font-weight-bold">Richiesta portale</div>
                    <div class="col-sm-8">
                        <span class="badge badge-info">
                            <i class="fas fa-link mr-1"></i>Richiesta #<?= $intervento['richiesta_id'] ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <div class="card-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <a href="<?= base_url('interventi') ?>" class="btn btn-secondary btn-sm mb-1">
                        <i class="fas fa-arrow-left mr-1"></i> Elenco
                    </a>
                    <div>
                        <a href="<?= base_url('interventi/' . $intervento['id'] . '/edit') ?>"
                           class="btn btn-warning btn-sm mb-1">
                            <i class="fas fa-edit mr-1"></i> Modifica
                        </a>
                        <?php if ($intervento['stato'] !== 'completato' && $intervento['stato'] !== 'annullato'): ?>
                            <button type="button" class="btn btn-success btn-sm mb-1"
                                    data-toggle="modal" data-target="#modalChiudi">
                                <i class="fas fa-check mr-1"></i> Chiudi
                            </button>
                        <?php endif; ?>
                        <a href="<?= base_url('interventi/' . $intervento['id'] . '/pdf') ?>"
                           target="_blank" class="btn btn-outline-secondary btn-sm mb-1">
                            <i class="fas fa-file-pdf mr-1"></i> PDF
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm mb-1"
                                data-toggle="modal" data-target="#modalEmail">
                            <i class="fas fa-envelope mr-1"></i> Invia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">

        <?php
            $mapLat = $intervento['lat'] ?? $intervento['cliente_lat'] ?? null;
            $mapLng = $intervento['lng'] ?? $intervento['cliente_lng'] ?? null;
            $mapSource = $intervento['lat'] ? 'intervento' : ($intervento['cliente_lat'] ? 'cliente' : null);
        ?>

        <?php if ($mapLat && $mapLng): ?>
        <div class="card card-outline card-secondary mb-3">
            <div class="card-header py-2">
                <h3 class="card-title small text-muted mb-0">Posizione</h3>
                <div class="card-tools">
                    <?php if ($mapSource === 'cliente'): ?>
                        <small class="text-muted"><i class="fas fa-user mr-1"></i>sede cliente</small>
                    <?php else: ?>
                        <small class="text-success"><i class="fas fa-map-marker-alt mr-1"></i>geocodificato</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="mappa-intervento" style="height:200px;"></div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title small text-muted">Creato il</h3>
            </div>
            <div class="card-body">
                <p class="mb-0"><?= date('d/m/Y \a\l\l\e H:i', strtotime($intervento['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal invio email rapportino -->
<div class="modal fade" id="modalEmail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('interventi/' . $intervento['id'] . '/invia-email') ?>" id="form-invia-email">
                <?= csrf_field() ?>
                <div class="modal-header" style="background:var(--clr-teal);color:#fff;">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope mr-2"></i>Invia rapportino per email
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="email_destinatario" class="font-weight-bold small">Indirizzo email destinatario</label>
                        <input type="email" name="email_destinatario" id="email_destinatario"
                               class="form-control"
                               value="<?= esc($intervento['cliente_email'] ?? '') ?>"
                               placeholder="email@esempio.it"
                               required>
                        <small class="text-muted">Il PDF verrà allegato e inviato a questo indirizzo.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" id="btn-invia-email" class="btn btn-info btn-sm">
                        <i class="fas fa-paper-plane mr-1"></i> Invia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal chiusura intervento -->
<?php $from = service('request')->getGet('from'); ?>
<div class="modal fade" id="modalChiudi" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('interventi/' . $intervento['id'] . '/chiudi') ?>">
                <?= csrf_field() ?>
                <?php if ($from): ?>
                    <input type="hidden" name="from" value="<?= esc($from) ?>">
                <?php endif; ?>
                <div class="modal-header" style="background:var(--clr-teal);color:#fff;">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle mr-2"></i>Chiudi intervento #<?= $intervento['id'] ?>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        L'intervento verrà marcato come <strong>completato</strong> con data e ora attuali.
                    </p>
                    <div class="form-group mb-0">
                        <label for="note_chiusura" class="font-weight-bold small">
                            Note di chiusura <span class="text-muted font-weight-normal">(opzionale)</span>
                        </label>
                        <textarea name="note_chiusura" id="note_chiusura" rows="4"
                                  class="form-control"
                                  placeholder="Es: lavoro completato, ricambi installati, cliente soddisfatto…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check mr-1"></i> Conferma chiusura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?php
    $mapLat = $intervento['lat'] ?? $intervento['cliente_lat'] ?? null;
    $mapLng = $intervento['lng'] ?? $intervento['cliente_lng'] ?? null;
?>
<?php if ($mapLat && $mapLng): ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var lat   = <?= (float) $mapLat ?>;
    var lng   = <?= (float) $mapLng ?>;
    var label = '<?= esc(addslashes($intervento['citta'] ?? $intervento['luogo_intervento'] ?? 'Intervento #' . $intervento['id'])) ?>';

    var map = L.map('mappa-intervento', { zoomControl: true, scrollWheelZoom: false })
               .setView([lat, lng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map).bindPopup(label).openPopup();
})();
</script>
<?= $this->endSection() ?>
<?php endif; ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('form-invia-email').addEventListener('submit', function () {
    var overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;';
    overlay.innerHTML = '<div class="spinner-border text-light" style="width:3rem;height:3rem;" role="status"></div>'
                      + '<span style="color:#fff;font-size:1rem;font-weight:500;">Invio email in corso&hellip;</span>';
    document.body.appendChild(overlay);
    document.getElementById('btn-invia-email').disabled = true;
});
</script>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/interventi/show') ?>
<?= $this->endSection() ?>
