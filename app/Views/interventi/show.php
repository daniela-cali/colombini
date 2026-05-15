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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Intervento #<?= $intervento['id'] ?></h3>
                <div class="d-flex align-items-center ml-auto">
                    <?php $s = $stati[$intervento['stato']] ?? ['label' => $intervento['stato'], 'badge' => 'badge-secondary']; ?>
                    <span class="badge badge-light text-dark px-3 py-2"><?= $s['label'] ?></span>
                    <?php $from = service('request')->getGet('from'); if ($from): ?>
                    <a href="<?= base_url($from) ?>" class="btn btn-sm btn-outline-light ml-2" title="Torna a <?= esc($from) ?>">
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
                            <a href="<?= base_url('interventi/' . $intervento['id'] . '/chiudi') ?>"
                               class="btn btn-success btn-sm mb-1"
                               onclick="return confirm('Chiudere l\'intervento come completato?')">
                                <i class="fas fa-check mr-1"></i> Chiudi
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('interventi/' . $intervento['id'] . '/pdf') ?>"
                           class="btn btn-outline-secondary btn-sm mb-1">
                            <i class="fas fa-print mr-1"></i> Stampa
                        </a>
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
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title small text-muted mb-0">Posizione</h3>
                <?php if ($mapSource === 'cliente'): ?>
                    <small class="text-muted"><i class="fas fa-user mr-1"></i>sede cliente</small>
                <?php else: ?>
                    <small class="text-success"><i class="fas fa-map-marker-alt mr-1"></i>geocodificato</small>
                <?php endif; ?>
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
