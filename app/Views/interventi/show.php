<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <?php $from = service('request')->getGet('from'); ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <?php if ($from === 'calendario'): ?>
        <li class="breadcrumb-item"><a href="<?= base_url('calendario') ?>">Calendario</a></li>
    <?php elseif ($from && str_starts_with($from, 'clienti/')): ?>
        <li class="breadcrumb-item"><a href="<?= base_url('clienti') ?>">Clienti</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url($from) ?>">Scheda cliente</a></li>
    <?php else: ?>
        <li class="breadcrumb-item"><a href="<?= base_url('interventi') ?>">Interventi</a></li>
    <?php endif; ?>
    <li class="breadcrumb-item active">#<?= $intervento['id'] ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Intervento #<?= $intervento['id'] ?></h3>
                <div class="card-tools">
                    <?php $s = $stati[$intervento['stato']] ?? ['label' => $intervento['stato'], 'badge' => 'badge-secondary']; ?>
                    <span class="badge badge-light text-dark px-3 py-2"><?= $s['label'] ?></span>
                    <?php $from = service('request')->getGet('from'); if ($from): ?>
                    <a href="<?= base_url($from) ?>" class="btn btn-tool ml-2" title="Torna a <?= esc($from) ?>">
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

                <?php if ($intervento['firma_tecnico']): ?>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Firma tecnico</div>
                    <div class="col-sm-8">
                        <?php if (str_starts_with($intervento['firma_tecnico'], 'data:image/png;base64,')): ?>
                            <img src="<?= $intervento['firma_tecnico'] ?>" alt="Firma tecnico"
                                 style="max-width:220px;max-height:80px;border:1px solid #dee2e6;border-radius:4px;background:#fff;display:block;">
                        <?php else: ?>
                            <span class="badge badge-secondary">
                                <i class="fas fa-check mr-1"></i>Presa visione
                            </span>
                        <?php endif; ?>
                        <?php if ($intervento['firma_tecnico_at']): ?>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-clock mr-1"></i><?= date('d/m/Y H:i', strtotime($intervento['firma_tecnico_at'])) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['firma_cliente']): ?>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Firma cliente</div>
                    <div class="col-sm-8">
                        <?php if (str_starts_with($intervento['firma_cliente'], 'data:image/png;base64,')): ?>
                            <img src="<?= $intervento['firma_cliente'] ?>" alt="Firma cliente"
                                 style="max-width:220px;max-height:80px;border:1px solid #dee2e6;border-radius:4px;background:#fff;display:block;">
                        <?php else: ?>
                            <span class="badge badge-secondary">
                                <i class="fas fa-check mr-1"></i>Presa visione
                            </span>
                        <?php endif; ?>
                        <?php if ($intervento['firma_at']): ?>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-clock mr-1"></i><?= date('d/m/Y H:i', strtotime($intervento['firma_at'])) ?>
                            </small>
                        <?php endif; ?>
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

                <?php if (!empty($materiali_forniti)): ?>
                <hr>
                <div class="row">
                    <div class="col-sm-4 text-muted small font-weight-bold">
                        <i class="fas fa-boxes mr-1"></i>Materiali forniti
                    </div>
                    <div class="col-sm-8">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($materiali_forniti as $mat): ?>
                            <li class="small">
                                <span class="badge badge-secondary mr-1"><?= esc($mat['quantita']) ?>×</span>
                                <strong><?= esc($mat['cod_articolo']) ?></strong> — <?= esc($mat['descrizione']) ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <div class="card-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <?php $from = service('request')->getGet('from'); ?>
                    <a href="<?= ($from && str_starts_with($from, 'clienti/')) ? base_url($from) : base_url('interventi') ?>"
                       class="btn btn-secondary btn-sm mb-1">
                        <i class="fas fa-arrow-left mr-1"></i>
                        <?= ($from && str_starts_with($from, 'clienti/')) ? 'Scheda cliente' : 'Elenco' ?>
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
                        <?php if ($intervento['stato'] === 'completato'): ?>
                        <a href="<?= base_url('interventi/' . $intervento['id'] . '/pdf') ?>"
                           target="_blank" class="btn btn-outline-secondary btn-sm mb-1">
                            <i class="fas fa-file-pdf mr-1"></i> PDF
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm mb-1"
                                data-toggle="modal" data-target="#modalEmail">
                            <i class="fas fa-envelope mr-1"></i> Invia
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-sm mb-1"
                                data-toggle="modal" data-target="#modalFirma">
                            <i class="fas fa-signature mr-1"></i>
                            <?= $intervento['firma_cliente'] ? 'Rif.' : 'Firma' ?>
                        </button>
                        <?php endif; ?>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="<?= base_url('interventi/' . $intervento['id'] . '/chiudi') ?>" id="form-chiudi">
                <?= csrf_field() ?>
                <input type="hidden" name="firma_tecnico_data" id="firma_tecnico_data">
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
                    <!-- Step 1: prompt materiali -->
                    <div id="step-prompt">
                        <div class="text-center py-4">
                            <i class="fas fa-boxes fa-3x text-muted mb-3 d-block"></i>
                            <p class="font-weight-bold mb-4">Hai consegnato materiali durante questa visita?</p>
                            <button type="button" id="btn-si-materiali" class="btn btn-success">
                                <i class="fas fa-check mr-1"></i> Sì, inserisci materiali
                            </button>
                            <button type="button" id="btn-no-materiali" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-right mr-1"></i> No, chiudi direttamente
                            </button>
                        </div>
                    </div>
                    <!-- Step 2: inserimento materiali -->
                    <div id="step-materiali" style="display:none">
                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-boxes mr-1"></i> Materiali forniti
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Articolo</th>
                                        <th style="width:100px">Qtà</th>
                                        <th style="width:40px"></th>
                                    </tr>
                                </thead>
                                <tbody id="materiali-container"></tbody>
                            </table>
                        </div>
                        <button type="button" id="btn-add-materiale" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-plus mr-1"></i> Aggiungi riga
                        </button>
                    </div>
                    <!-- Step 3: note e firma tecnico -->
                    <div id="step-chiusura" style="display:none">
                        <p class="text-muted small mb-3">
                            L'intervento verrà marcato come <strong>completato</strong> con data e ora attuali.
                        </p>
                        <div class="form-group">
                            <label for="note_chiusura" class="font-weight-bold small">
                                Note di chiusura <span class="text-muted font-weight-normal">(opzionale)</span>
                            </label>
                            <textarea name="note_chiusura" id="note_chiusura" rows="3"
                                      class="form-control"
                                      placeholder="Es: lavoro completato, ricambi installati, cliente soddisfatto…"></textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold small">
                                Firma tecnico <span class="text-muted font-weight-normal">(opzionale)</span>
                            </label>
                            <div style="border:1px solid #ced4da;border-radius:.25rem;background:#fff;touch-action:none;">
                                <canvas id="canvas-firma-tecnico" style="width:100%;display:block;"></canvas>
                            </div>
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                <button type="button" id="btn-clear-firma-tecnico" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eraser mr-1"></i>Cancella
                                </button>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="chk-presa-visione-tecnico"
                                           name="presa_visione_tecnico" value="1">
                                    <label class="custom-control-label small" for="chk-presa-visione-tecnico">
                                        Presa visione senza firma
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footer-prompt">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Annulla</button>
                </div>
                <div class="modal-footer" id="footer-materiali" style="display:none">
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-back-from-mat">
                        <i class="fas fa-arrow-left mr-1"></i> Indietro
                    </button>
                    <button type="button" class="btn btn-primary btn-sm ml-2" id="btn-continua-chiusura">
                        Continua <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
                <div class="modal-footer" id="footer-chiusura" style="display:none">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success btn-sm ml-2">
                        <i class="fas fa-check mr-1"></i> Conferma chiusura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal raccolta firma cliente -->
<?php if ($intervento['stato'] === 'completato'): ?>
<div class="modal fade" id="modalFirma" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('interventi/' . $intervento['id'] . '/firma') ?>" id="form-firma">
                <?= csrf_field() ?>
                <input type="hidden" name="firma_data" id="firma_data">
                <div class="modal-header" style="background:var(--clr-teal);color:#fff;">
                    <h5 class="modal-title">
                        <i class="fas fa-signature mr-2"></i>Firma cliente
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <?php if ($intervento['firma_cliente']): ?>
                    <div class="alert alert-warning py-2 small mb-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        È già presente una firma. Salvando, verrà sovrascritta.
                    </div>
                    <?php endif; ?>
                    <p class="text-muted small mb-2">Firmare nell'area sottostante usando il dito o lo stilo.</p>
                    <div style="border:1px solid #ced4da;border-radius:.25rem;background:#fff;touch-action:none;">
                        <canvas id="canvas-firma" style="width:100%;display:block;"></canvas>
                    </div>
                    <div class="mt-2 text-right">
                        <button type="button" id="btn-clear-firma" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eraser mr-1"></i>Cancella
                        </button>
                    </div>

                    <div class="d-flex align-items-center my-3">
                        <hr class="flex-grow-1 m-0"><span class="px-2 text-muted small">oppure</span><hr class="flex-grow-1 m-0">
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="chk-presa-visione" name="presa_visione" value="1">
                        <label class="custom-control-label small" for="chk-presa-visione">
                            Il cliente dichiara di aver preso visione del rapportino
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" id="btn-salva-firma" class="btn btn-success btn-sm" disabled>
                        <i class="fas fa-save mr-1"></i>Salva firma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

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

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script>
(function () {
    var ARTICOLI     = <?= json_encode(array_values($articoli)) ?>;
    var ABITUALI_IDS = <?= json_encode(array_map('intval', $abituali_ids)) ?>;
    var matIdx = 0;

    function makeOpt(a) {
        var o = document.createElement('option');
        o.value = a.id;
        o.textContent = '[' + a.cod_articolo + '] ' + a.descrizione;
        return o;
    }

    function buildMatOpts(sel) {
        var abituali = ARTICOLI.filter(function (a) { return ABITUALI_IDS.indexOf(parseInt(a.id)) >= 0; });
        var altri    = ARTICOLI.filter(function (a) { return ABITUALI_IDS.indexOf(parseInt(a.id)) < 0; });
        sel.innerHTML = '<option value="">— seleziona articolo —</option>';
        if (abituali.length) {
            var g = document.createElement('optgroup');
            g.label = 'Abituali per questo cliente';
            abituali.forEach(function (a) { g.appendChild(makeOpt(a)); });
            sel.appendChild(g);
        }
        var g2 = document.createElement('optgroup');
        g2.label = abituali.length ? 'Altri articoli' : 'Tutti gli articoli';
        altri.forEach(function (a) { g2.appendChild(makeOpt(a)); });
        sel.appendChild(g2);
    }

    function addMatRiga() {
        var idx = matIdx++;
        var tr  = document.createElement('tr');

        var tdA = document.createElement('td');
        var sel = document.createElement('select');
        sel.name      = 'materiali[' + idx + '][articolo_id]';
        sel.className = 'form-control form-control-sm';
        buildMatOpts(sel);
        tdA.appendChild(sel);
        tr.appendChild(tdA);

        var tdQ  = document.createElement('td');
        var inpQ = document.createElement('input');
        inpQ.type        = 'number';
        inpQ.name        = 'materiali[' + idx + '][quantita]';
        inpQ.className   = 'form-control form-control-sm';
        inpQ.min         = 1;
        inpQ.step        = 1;
        inpQ.placeholder = '1';
        tdQ.appendChild(inpQ);
        tr.appendChild(tdQ);

        var tdR  = document.createElement('td');
        tdR.className = 'text-center';
        var btnR = document.createElement('button');
        btnR.type      = 'button';
        btnR.className = 'btn btn-sm btn-outline-danger';
        btnR.innerHTML = '<i class="fas fa-times"></i>';
        btnR.addEventListener('click', function () {
            var rows = document.querySelectorAll('#materiali-container tr');
            if (rows.length > 1) {
                if ($(sel).data('select2')) { $(sel).select2('destroy'); }
                tr.remove();
            } else {
                if ($(sel).data('select2')) { $(sel).val('').trigger('change'); }
                inpQ.value = '';
            }
        });
        tdR.appendChild(btnR);
        tr.appendChild(tdR);

        document.getElementById('materiali-container').appendChild(tr);
        $(sel).select2({
            placeholder:    '— seleziona articolo —',
            width:          '100%',
            dropdownParent: $('#modalChiudi'),
        });
    }

    function showStep(step) {
        ['prompt', 'materiali', 'chiusura'].forEach(function (s) {
            var vis = s === step ? '' : 'none';
            document.getElementById('step-'   + s).style.display = vis;
            document.getElementById('footer-' + s).style.display = vis;
        });
        if (step === 'chiusura') {
            setTimeout(function () { $('#modalChiudi').trigger('chiusura:show'); }, 50);
        }
    }

    document.getElementById('btn-si-materiali').addEventListener('click', function () {
        showStep('materiali');
        if (document.getElementById('materiali-container').children.length === 0) { addMatRiga(); }
    });

    document.getElementById('btn-no-materiali').addEventListener('click', function () { showStep('chiusura'); });

    document.getElementById('btn-back-from-mat').addEventListener('click', function () { showStep('prompt'); });

    document.getElementById('btn-continua-chiusura').addEventListener('click', function () { showStep('chiusura'); });

    document.getElementById('btn-add-materiale').addEventListener('click', function () { addMatRiga(); });

    $('#modalChiudi').on('hidden.bs.modal', function () {
        showStep('prompt');
        document.getElementById('materiali-container').innerHTML = '';
        matIdx = 0;
    });
})();
</script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
(function () {
    var canvas   = document.getElementById('canvas-firma-tecnico');
    var chk      = document.getElementById('chk-presa-visione-tecnico');
    var padTec;

    function resizeCanvasTec() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width  = canvas.offsetWidth * ratio;
        canvas.height = Math.round(canvas.offsetWidth * 0.3) * ratio;
        canvas.style.height = Math.round(canvas.offsetWidth * 0.3) + 'px';
        canvas.getContext('2d').scale(ratio, ratio);
        if (padTec) padTec.clear();
    }

    $('#modalChiudi').on('chiusura:show', function () {
        if (!padTec) {
            resizeCanvasTec();
            padTec = new SignaturePad(canvas, { penColor: '#1f2937' });
        }
    });

    document.getElementById('btn-clear-firma-tecnico').addEventListener('click', function () {
        if (padTec) padTec.clear();
    });

    document.getElementById('form-chiudi').addEventListener('submit', function () {
        if (padTec && !padTec.isEmpty()) {
            document.getElementById('firma_tecnico_data').value = padTec.toDataURL('image/png');
        }
    });
})();
</script>
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

<?php if ($intervento['stato'] === 'completato'): ?>
<?= $this->section('scripts') ?>
<script>
(function () {
    var canvas   = document.getElementById('canvas-firma');
    var chk      = document.getElementById('chk-presa-visione');
    var pad;
    var btnSalva = document.getElementById('btn-salva-firma');
    var btnClear = document.getElementById('btn-clear-firma');

    function updateSalva() {
        btnSalva.disabled = !(chk.checked || (pad && !pad.isEmpty()));
    }

    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width  = canvas.offsetWidth  * ratio;
        canvas.height = Math.round(canvas.offsetWidth * 0.35) * ratio;
        canvas.style.height = Math.round(canvas.offsetWidth * 0.35) + 'px';
        canvas.getContext('2d').scale(ratio, ratio);
        if (pad) pad.clear();
    }

    $('#modalFirma').on('shown.bs.modal', function () {
        if (!pad) {
            resizeCanvas();
            pad = new SignaturePad(canvas, { penColor: '#1f2937' });
            pad.addEventListener('endStroke', updateSalva);
        }
    });

    btnClear.addEventListener('click', function () {
        if (pad) pad.clear();
        updateSalva();
    });

    chk.addEventListener('change', updateSalva);

    document.getElementById('form-firma').addEventListener('submit', function (e) {
        var hasFirma = pad && !pad.isEmpty();
        var hasPresa = chk.checked;
        if (!hasFirma && !hasPresa) { e.preventDefault(); return; }
        if (hasFirma) {
            document.getElementById('firma_data').value = pad.toDataURL('image/png');
        }
    });
})();
</script>
<?= $this->endSection() ?>
<?php endif; ?>

<?= $this->section('help') ?>
<?= $this->include('help/interventi/show') ?>
<?= $this->endSection() ?>
