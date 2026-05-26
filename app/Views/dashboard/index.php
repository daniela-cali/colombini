<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item active">Dashboard</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Widget contatori -->
<div class="row">
    <div class="col-6 col-lg-3">
        <a href="<?= base_url('interventi?stato=da_pianificare') ?>" class="info-box" style="text-decoration:none;color:inherit;">
            <span class="info-box-icon bg-secondary"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Da pianificare</span>
                <span class="info-box-number"><?= $stats['da_pianificare'] ?></span>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="<?= base_url('interventi?stato=pianificato') ?>" class="info-box" style="text-decoration:none;color:inherit;">
            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pianificati</span>
                <span class="info-box-number"><?= $stats['pianificati'] ?></span>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="<?= base_url('interventi?stato=in_corso') ?>" class="info-box" style="text-decoration:none;color:inherit;">
            <span class="info-box-icon bg-warning"><i class="fas fa-spinner"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">In corso</span>
                <span class="info-box-number"><?= $stats['in_corso'] ?></span>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="<?= base_url('interventi?stato=completato') ?>" class="info-box" style="text-decoration:none;color:inherit;">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Completati questo mese</span>
                <span class="info-box-number"><?= $stats['completati_mese'] ?></span>
            </div>
        </a>
    </div>
</div>

<!-- Quaderni (da pianificare per tipo) -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list mr-1"></i> Da pianificare</h3>
                <div class="card-tools">
                    <a href="<?= base_url('quaderni') ?>" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-th-list mr-1"></i> Vista quaderni
                    </a>
                    <a href="<?= base_url('interventi/new') ?>" class="btn btn-sm btn-success ml-1">
                        <i class="fas fa-plus mr-1"></i> Nuovo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($quaderni)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p class="mb-0">Nessun intervento da pianificare.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($quaderni as $tipo => $interventi): ?>
                        <div class="col-md-6 col-xl-4 mb-3">
                            <div class="card card-outline card-secondary mb-0 h-100">
                                <div class="card-header p-2">
                                    <h6 class="card-title mb-0 small">
                                        <i class="fas <?= esc($icone[$tipo] ?? 'fa-tools') ?> mr-1 text-muted"></i>
                                        <?= esc($tipi[$tipo] ?? $tipo) ?>
                                    </h6>
                                    <div class="card-tools">
                                        <span class="badge badge-secondary"><?= count($interventi) ?></span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <?php foreach (array_slice($interventi, 0, 5) as $inv):
                                            $cliente = $inv['cliente_ragsoc']
                                                ?: trim(($inv['cliente_cognome'] ?? '') . ' ' . ($inv['cliente_nome'] ?? ''));
                                        ?>
                                        <li class="list-group-item px-2 py-1 d-flex justify-content-between align-items-center">
                                            <div style="min-width:0;">
                                                <div class="small font-weight-bold text-truncate">
                                                    <?= $cliente ? esc($cliente) : '<span class="text-muted">—</span>' ?>
                                                </div>
                                                <?php if ($inv['descrizione']): ?>
                                                <div class="text-muted" style="font-size:.72rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;">
                                                    <?= esc($inv['descrizione']) ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-2 text-nowrap">
                                                <button type="button"
                                                    class="btn btn-xs btn-outline-primary btn-pianifica"
                                                    data-id="<?= $inv['id'] ?>"
                                                    data-cliente="<?= esc($cliente) ?>"
                                                    data-tipo="<?= esc($tipo) ?>"
                                                    data-cliente-id="<?= (int)($inv['cliente_id'] ?? 0) ?>"
                                                    data-descrizione="<?= esc(str_replace(["\r\n", "\r", "\n"], ' ', $inv['descrizione'] ?? '')) ?>"
                                                    title="Pianifica">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </button>
                                                <a href="<?= base_url('interventi/' . $inv['id']) ?>"
                                                   class="btn btn-xs btn-outline-secondary" title="Apri scheda">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                        <?php if (count($interventi) > 5): ?>
                                        <li class="list-group-item px-2 py-1 text-center">
                                            <a href="<?= base_url('interventi?stato=da_pianificare&tipo_intervento=' . urlencode($tipo)) ?>"
                                               class="small text-muted">
                                                + altri <?= count($interventi) - 5 ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Richieste portale -->
<?php if (!empty($ultime_richieste)): ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-inbox mr-1"></i> Richieste portale</h3>
                <div class="card-tools">
                    <?php if ($nuove_richieste > 0): ?>
                        <span class="badge badge-info mr-2"><?= $nuove_richieste ?> nuove</span>
                    <?php endif; ?>
                    <a href="<?= base_url('richieste') ?>" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-list mr-1"></i> Tutte
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Richiedente</th>
                                <th class="d-none d-md-table-cell">Tipo</th>
                                <th class="d-none d-sm-table-cell">Data</th>
                                <th>Stato</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ultime_richieste as $r): ?>
                            <tr>
                                <td class="align-middle"><?= esc($r['richiedente']) ?></td>
                                <td class="d-none d-md-table-cell align-middle">
                                    <i class="fas <?= esc($icone[$r['tipo_intervento']] ?? 'fa-tools') ?> mr-1 text-muted"></i>
                                    <?= esc($tipi[$r['tipo_intervento']] ?? $r['tipo_intervento']) ?>
                                </td>
                                <td class="d-none d-sm-table-cell align-middle text-muted small">
                                    <?= date('d/m/Y', strtotime($r['created_at'])) ?>
                                </td>
                                <td class="align-middle">
                                    <?php $sr = $stati_richiesta[$r['stato']] ?? ['label' => $r['stato'], 'badge' => 'badge-secondary']; ?>
                                    <span class="badge <?= $sr['badge'] ?>"><?= $sr['label'] ?></span>
                                </td>
                                <td class="align-middle text-right">
                                    <?php if ($r['stato'] === 'nuova'): ?>
                                        <a href="<?= base_url('interventi/new?richiesta_id=' . $r['id']) ?>"
                                           class="btn btn-xs btn-outline-success" title="Crea intervento">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('richieste/' . $r['id']) ?>"
                                       class="btn btn-xs btn-outline-secondary" title="Apri">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Pianifica -->
<div class="modal fade" id="modalPianifica" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><i class="fas fa-calendar-plus mr-1"></i> Pianifica intervento</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="form-pianifica" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="stato" value="pianificato">
                <div class="modal-body">
                    <p class="mb-1 font-weight-bold" id="pianifica-cliente"></p>
                    <p id="pianifica-descrizione" class="small text-muted mb-3" style="white-space:pre-wrap;"></p>
                    <div class="form-group">
                        <label>Data e ora <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="data_pianificata" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>Tecnico</label>
                        <select name="tecnico_id" id="pianifica-tecnico" class="form-control">
                            <option value="">— Non assegnato —</option>
                            <?php foreach ($tecnici as $t): ?>
                                <option value="<?= $t->id ?>"><?= esc($t->cognome . ' ' . $t->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="pianifica-suggerimento" class="mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check mr-1"></i> Pianifica
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.btn-pianifica').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var id        = this.dataset.id;
        var cliente   = this.dataset.cliente;
        var tipo      = this.dataset.tipo;
        var clienteId = this.dataset.clienteId;
        var descr     = this.dataset.descrizione;

        document.getElementById('pianifica-cliente').textContent    = cliente || '—';
        document.getElementById('pianifica-descrizione').textContent = descr || '';
        document.getElementById('pianifica-descrizione').style.display = descr ? '' : 'none';
        document.getElementById('pianifica-suggerimento').innerHTML  = '';
        document.getElementById('pianifica-tecnico').value           = '';
        document.getElementById('form-pianifica').action = '<?= base_url('interventi/') ?>' + id;

        if (tipo) {
            fetch('<?= base_url('interventi/api/tecnico-consigliato') ?>?tipo_intervento=' + encodeURIComponent(tipo) + '&cliente_id=' + encodeURIComponent(clienteId || 0))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.tecnico) return;
                    var t    = data.tecnico;
                    var sugg = document.getElementById('pianifica-suggerimento');
                    sugg.innerHTML =
                        '<div class="alert alert-info py-1 px-2 mb-0 small">' +
                        '<i class="fas fa-lightbulb mr-1"></i>Consigliato: <strong>' + t.cognome + ' ' + t.nome + '</strong>' +
                        ' <span class="text-muted">(' + t.cnt + ' interventi simili)</span>' +
                        ' <a href="#" class="ml-2" onclick="document.getElementById(\'pianifica-tecnico\').value=\'' + t.tecnico_id + '\';this.parentElement.remove();return false;">Usa questo</a>' +
                        '</div>';
                });
        }

        $('#modalPianifica').modal('show');
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/dashboard/index') ?>
<?= $this->endSection() ?>
