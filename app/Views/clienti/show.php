<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <?php $_from = service('request')->getGet('from'); ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <?php if ($_from === 'richieste'): ?>
    <li class="breadcrumb-item"><a href="<?= base_url('richieste') ?>">Richieste</a></li>
    <?php else: ?>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti') ?>">Clienti</a></li>
    <?php endif; ?>
    <li class="breadcrumb-item active"><?= esc($nome_display) ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">

    <!-- Colonna sinistra: anagrafica -->
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header py-2">
                <h3 class="card-title">Scheda cliente</h3>
                <?php if ($_from): ?>
                <div class="card-tools">
                    <a href="<?= base_url($_from) ?>" class="btn btn-tool" title="Torna indietro">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body text-center pt-4">
                <div class="mb-3">
                    <?php if ($cliente['tipo'] === 'persona_fisica'): ?>
                        <i class="fas fa-user-circle fa-5x" style="color: var(--clr-teal);"></i>
                    <?php else: ?>
                        <i class="fas fa-building fa-5x" style="color: var(--clr-teal);"></i>
                    <?php endif; ?>
                </div>
                <h4 class="mb-1"><?= esc($nome_display) ?></h4>
                <p class="text-muted small mb-2"><?= esc($cliente['codice']) ?></p>
                <span class="badge badge-light px-3 py-1">
                    <?= $cliente['tipo'] === 'persona_fisica' ? 'Persona fisica' : 'Società / Ditta' ?>
                </span>

                <?php if ($cliente['telefono']): ?>
                    <p class="mt-3 mb-1">
                        <i class="fas fa-phone mr-1 text-muted"></i>
                        <a href="tel:<?= esc($cliente['telefono']) ?>"><?= esc($cliente['telefono']) ?></a>
                    </p>
                <?php endif; ?>
                <?php if ($cliente['email']): ?>
                    <p class="mb-1">
                        <i class="fas fa-envelope mr-1 text-muted"></i>
                        <a href="mailto:<?= esc($cliente['email']) ?>"><?= esc($cliente['email']) ?></a>
                    </p>
                <?php endif; ?>
            </div>
            <div class="card-footer clearfix">
                <a href="<?= base_url('clienti') ?>" class="btn btn-sm btn-secondary float-left">
                    <i class="fas fa-arrow-left mr-1"></i> Elenco
                </a>
                <a href="<?= base_url('clienti/' . $cliente['id'] . '/edit') ?>" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-edit mr-1"></i> Modifica
                </a>
            </div>
        </div>

        <!-- Accesso portale -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-globe mr-1"></i> Accesso portale</h3>
            </div>
            <div class="card-body">
                <?php if ($utente): ?>
                    <p class="mb-1">
                        <i class="fas fa-user mr-1 text-muted"></i>
                        <strong><?= esc($utente->username) ?></strong>
                    </p>
                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Attivo</span>
                <?php else: ?>
                    <p class="text-muted small mb-2">Nessun accesso portale configurato.</p>
                    <a href="<?= base_url('clienti/' . $cliente['id'] . '/portale') ?>"
                       class="btn btn-sm btn-outline-primary btn-block">
                        <i class="fas fa-plus mr-1"></i> Crea accesso portale
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Elimina -->
        <div class="card card-outline card-danger">
            <div class="card-body text-center">
                <form method="post"
                      action="<?= base_url('clienti/' . $cliente['id'] . '/elimina') ?>"
                      onsubmit="return confirm('Eliminare il cliente <?= esc($nome_display) ?>?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger btn-sm btn-block">
                        <i class="fas fa-trash mr-1"></i> Elimina cliente
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Colonna destra: dettagli -->
    <div class="col-md-8">
        <div class="card  card-primary">
            <div class="card-header">
                <h3 class="card-title">Dati anagrafici</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <?php if ($cliente['tipo'] === 'societa' && $cliente['ragsoc']): ?>
                        <tr>
                            <th class="text-muted" width="35%">Ragione sociale</th>
                            <td><?= esc($cliente['ragsoc']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['tipo'] === 'persona_fisica'): ?>
                        <?php if ($cliente['cognome']): ?>
                            <tr>
                                <th class="text-muted">Cognome</th>
                                <td><?= esc($cliente['cognome']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($cliente['nome']): ?>
                            <tr>
                                <th class="text-muted">Nome</th>
                                <td><?= esc($cliente['nome']) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($cliente['piva']): ?>
                        <tr>
                            <th class="text-muted">P.IVA</th>
                            <td><?= esc($cliente['piva']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['cfisc']): ?>
                        <tr>
                            <th class="text-muted">Cod. Fiscale</th>
                            <td><?= esc($cliente['cfisc']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['indirizzo'] || $cliente['citta']): ?>
                        <tr>
                            <th class="text-muted">Indirizzo</th>
                            <td>
                                <?= esc($cliente['indirizzo'] ?? '') ?>
                                <?php if ($cliente['citta']): ?>
                                    <br><?= esc(trim(($cliente['cap'] ? $cliente['cap'] . ' ' : '') . $cliente['citta'] . ($cliente['provincia'] ? ' (' . $cliente['provincia'] . ')' : ''))) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['telefono']): ?>
                        <tr>
                            <th class="text-muted">Telefono</th>
                            <td><a href="tel:<?= esc($cliente['telefono']) ?>"><?= esc($cliente['telefono']) ?></a></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['email']): ?>
                        <tr>
                            <th class="text-muted">Email</th>
                            <td><a href="mailto:<?= esc($cliente['email']) ?>"><?= esc($cliente['email']) ?></a></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['note']): ?>
                        <tr>
                            <th class="text-muted">Note</th>
                            <td><?= nl2br(esc($cliente['note'])) ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <?php if (empty($cliente['geocoded_at'])): ?>
        <?php if ($cliente['geocodifica_fallita'] ?? 0): ?>
        <div class="card card-outline card-warning" id="card-geo-fallita">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-lg mr-3 flex-shrink-0"></i>
                    <div class="flex-grow-1">
                        <strong class="text-warning">Geocodifica fallita</strong><br>
                        <small class="text-muted">L'indirizzo non è stato trovato. Verifica i dati nel form di modifica oppure riprova.</small>
                    </div>
                    <button id="btn-geocodifica" type="button" class="btn btn-sm btn-outline-warning ml-3 flex-shrink-0">
                        <i class="fas fa-redo mr-1"></i> Riprova
                    </button>
                </div>
                <div id="geo-msg" class="mt-2" style="display:none;"></div>
            </div>
        </div>
        <?php else: ?>
        <div class="card card-outline card-danger">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle text-danger fa-lg mr-3"></i>
                    <div>
                        <strong class="text-danger">Coordinate mancanti</strong><br>
                        <small class="text-muted">Questo cliente non è geocodificato e verrà escluso dall'ottimizzazione dei percorsi. Verifica l'indirizzo e usa il pulsante "Verifica indirizzo" nel form di modifica.</small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($cliente['lat']) && !empty($cliente['lng'])): ?>
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i> Posizione</h3>
                <div class="card-tools">
                    <a href="https://www.openstreetmap.org/?mlat=<?= $cliente['lat'] ?>&mlon=<?= $cliente['lng'] ?>#map=15/<?= $cliente['lat'] ?>/<?= $cliente['lng'] ?>"
                       target="_blank" class="btn btn-tool" title="Apri in OpenStreetMap">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="mappa-cliente" style="height:260px;"></div>
            </div>
            <?php if ($cliente['geocoded_at']): ?>
            <div class="card-footer text-muted small">
                <i class="fas fa-clock mr-1"></i> Geocodificato il <?= date('d/m/Y', strtotime($cliente['geocoded_at'])) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

</div>

<!-- Materiali & Note per il cliente -->
<div class="card card-info mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-boxes mr-1"></i> Materiali & Note
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#modalAddMateriale">
                <i class="fas fa-plus mr-1"></i> Aggiungi
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-tabs px-3 pt-2 border-bottom-0" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tab-note" role="tab">
                    Note
                    <?php if (!empty($note_solo_note)): ?>
                    <span class="badge badge-secondary ml-1"><?= count($note_solo_note) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-da-portare" role="tab">
                    Da portare
                    <?php if (!empty($note_articoli_da_portare)): ?>
                    <span class="badge badge-secondary ml-1"><?= count($note_articoli_da_portare) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-portati" role="tab">
                    Già portati
                    <?php if (!empty($note_articoli_portati)): ?>
                    <span class="badge badge-secondary ml-1"><?= count($note_articoli_portati) ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">

            <!-- Tab: note testuali -->
            <div class="tab-pane active" id="tab-note" role="tabpanel">
                <?php if (empty($note_solo_note)): ?>
                <p class="text-muted small p-3 mb-0">Nessuna nota. Usa "Aggiungi" lasciando l'articolo vuoto.</p>
                <?php else: ?>
                <?= view('clienti/_nota_rows', ['righe' => $note_solo_note, 'eliminabile' => true]) ?>
                <?php endif; ?>
            </div>

            <!-- Tab: articoli da portare -->
            <div class="tab-pane" id="tab-da-portare" role="tabpanel">
                <?php if (empty($note_articoli_da_portare)): ?>
                <p class="text-muted small p-3 mb-0">Nessun articolo da portare. Usa "Aggiungi" per inserirne uno.</p>
                <?php else: ?>
                <?= view('clienti/_nota_rows', ['righe' => $note_articoli_da_portare, 'eliminabile' => true]) ?>
                <?php endif; ?>
            </div>

            <!-- Tab: articoli già portati (storico) -->
            <div class="tab-pane" id="tab-portati" role="tabpanel">
                <?php if (empty($note_articoli_portati)): ?>
                <p class="text-muted small p-3 mb-0">Nessun articolo ancora consegnato.</p>
                <?php else: ?>
                <?= view('clienti/_nota_rows', ['righe' => $note_articoli_portati, 'eliminabile' => false]) ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- Modal aggiunta materiale / nota -->
<div class="modal fade" id="modalAddMateriale" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('clienti/' . $cliente['id'] . '/materiali') ?>">
                <?= csrf_field() ?>
                <div class="modal-header" style="background:var(--clr-teal);color:#fff;">
                    <h5 class="modal-title">
                        <i class="fas fa-box mr-2"></i> Aggiungi materiale / nota
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">
                            Articolo <span class="text-muted font-weight-normal">(opzionale)</span>
                        </label>
                        <select name="articolo_id" id="sel-mat-articolo" class="form-control">
                            <option value="">— nessun articolo specifico —</option>
                            <?php foreach ($articoli as $art): ?>
                            <option value="<?= $art['id'] ?>">[<?= esc($art['cod_articolo']) ?>] <?= esc($art['descrizione']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" id="row-mat-quantita" style="display:none;">
                        <label class="small font-weight-bold">Quantità</label>
                        <input type="number" name="quantita" class="form-control" min="0.01" step="0.01" placeholder="es. 2">
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">
                            Note <span class="text-muted font-weight-normal">(opzionale)</span>
                        </label>
                        <textarea name="note" class="form-control" rows="2"
                                  placeholder="Es: portare guanti, verificare filtro pompa…"></textarea>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <button type="button" class="btn btn-secondary float-left" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-save mr-1"></i> Salva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Storico interventi -->
<div class="card card-outline card-secondary mt-3">
    <div class="card-header py-2">
        <h3 class="card-title">
            <i class="fas fa-history mr-1"></i> Storico interventi
            <?php if (!empty($interventi)): ?>
            <span class="badge badge-secondary ml-1"><?= count($interventi) ?></span>
            <?php endif; ?>
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('interventi/new?cliente_id=' . $cliente['id']) ?>" class="btn btn-sm">
                <i class="fas fa-plus mr-1"></i> Nuovo intervento
            </a>
        </div>
    </div>
    <?php if (empty($interventi)): ?>
    <div class="card-body text-muted small py-3 text-center">
        Nessun intervento registrato per questo cliente.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th class="d-none d-md-table-cell">Tecnico</th>
                    <th>Stato</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($interventi as $inv):
                $s = $stati[$inv['stato']] ?? ['label' => $inv['stato'], 'badge' => 'badge-secondary'];
                $dataInv = $inv['data_pianificata']
                    ? date('d/m/Y', strtotime($inv['data_pianificata']))
                    : '—';
                $tecnico = trim(($inv['tecnico_cognome'] ?? '') . ' ' . ($inv['tecnico_nome'] ?? '')) ?: '—';
            ?>
                <tr>
                    <td class="align-middle text-nowrap small"><?= $dataInv ?></td>
                    <td class="align-middle small"><?= esc($inv['tipo_intervento']) ?></td>
                    <td class="d-none d-md-table-cell align-middle small text-muted"><?= esc($tecnico) ?></td>
                    <td class="align-middle"><span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span></td>
                    <td class="align-middle text-right">
                        <a href="<?= base_url('interventi/' . $inv['id'] . '?from=clienti/' . $cliente['id']) ?>" class="btn btn-xs btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/clienti/show') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script>
(function () {
    var sel = document.getElementById('sel-mat-articolo');
    var rowQ = document.getElementById('row-mat-quantita');

    $('#modalAddMateriale').on('shown.bs.modal', function () {
        $(sel).select2({ dropdownParent: $('#modalAddMateriale'), width: '100%', placeholder: '— nessun articolo specifico —' });
    });

    $(sel).on('change', function () {
        rowQ.style.display = this.value ? '' : 'none';
        if (!this.value) rowQ.querySelector('input').value = '';
    });

    $('#modalAddMateriale').on('hidden.bs.modal', function () {
        if ($(sel).data('select2')) { $(sel).val('').trigger('change'); }
        this.querySelector('textarea[name=note]').value = '';
    });
})();
</script>
<?= $this->endSection() ?>

<?php if ($cliente['geocodifica_fallita'] ?? 0): ?>
<?= $this->section('scripts') ?>
<script>
(function () {
    var btn = document.getElementById('btn-geocodifica');
    if (! btn) return;
    btn.addEventListener('click', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> In corso…';
        fetch('<?= base_url('clienti/' . $cliente['id'] . '/geocodifica') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            if (json.esito === 'ok') {
                location.reload();
            } else {
                var msg = document.getElementById('geo-msg');
                msg.innerHTML = '<small class="text-danger"><i class="fas fa-times-circle mr-1"></i>' +
                    (json.msg || 'Geocodifica fallita.') + '</small>';
                msg.style.display = '';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-redo mr-1"></i> Riprova';
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-redo mr-1"></i> Riprova';
        });
    });
})();
</script>
<?= $this->endSection() ?>
<?php endif; ?>

<?php if (!empty($cliente['lat']) && !empty($cliente['lng'])): ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var lat = <?= (float) $cliente['lat'] ?>;
    var lng = <?= (float) $cliente['lng'] ?>;
    var map = L.map('mappa-cliente', { zoomControl: true, scrollWheelZoom: false }).setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);
    L.marker([lat, lng]).addTo(map)
        .bindPopup('<?= esc(addslashes($nome_display)) ?><br><small><?= esc(addslashes(trim(($cliente['indirizzo'] ?? '') . ', ' . ($cliente['citta'] ?? '')))) ?></small>')
        .openPopup();
})();
</script>
<?= $this->endSection() ?>
<?php endif; ?>