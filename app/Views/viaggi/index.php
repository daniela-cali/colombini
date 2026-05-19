<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Viaggi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$bozze     = array_values(array_filter($viaggi, fn($v) => $v['stato'] === 'bozza'));
$approvati = array_values(array_filter($viaggi, fn($v) => $v['stato'] !== 'bozza'));
?>

<!-- Filtro data -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">
        <i class="fas fa-calendar-day mr-1 text-muted"></i>
        <?= data_ita($data) ?>
    </h5>
    <div class="d-flex align-items-center" style="gap:.5rem;">
        <?php if (!empty($approvati)): ?>
            <a href="<?= base_url('viaggi/pdf/' . $data) ?>" target="_blank"
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-print mr-1"></i> Stampa riepilogo
            </a>
        <?php endif; ?>
        <form method="get" action="<?= base_url('viaggi') ?>" class="mb-0">
            <input type="date" name="data" class="form-control form-control-sm"
                   value="<?= esc($data) ?>"
                   onchange="this.form.submit()">
        </form>
    </div>
</div>

<?php if (empty($viaggi)): ?>
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-route fa-3x mb-3"></i>
            <p>Nessun viaggio pianificato per il <strong><?= data_ita($data, false) ?></strong>.</p>
        </div>
    </div>
<?php else: ?>

    <?php if (!empty($bozze)): ?>
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-pencil-alt mr-1"></i> Bozze
            </h3>
            <div class="card-tools">
                <span class="badge badge-warning"><?= count($bozze) ?></span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Tecnico</th>
                        <th>Veicolo</th>
                        <th class="d-none d-md-table-cell">Tappe</th>
                        <th class="d-none d-md-table-cell">Distanza</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bozze as $v): ?>
                    <tr>
                        <td class="align-middle">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                                background:<?= esc($v['colore'] ?? '#6c757d') ?>;margin-right:6px;"></span>
                            <?= esc($v['cognome'] . ' ' . $v['nome']) ?>
                        </td>
                        <td class="align-middle">
                            <?= $v['veicolo_nome']
                                ? esc($v['veicolo_nome'] . ($v['veicolo_targa'] ? ' - ' . $v['veicolo_targa'] : ''))
                                : '<span class="text-muted small"><i class="fas fa-exclamation-circle mr-1 text-warning"></i>Non assegnato</span>' ?>
                        </td>
                        <td class="d-none d-md-table-cell align-middle">
                            <?php if ((int)$v['tappe_count'] > 0): ?>
                                <span class="badge badge-light border">
                                    <?= (int)$v['tappe_count'] ?> tapp<?= (int)$v['tappe_count'] === 1 ? 'a' : 'e' ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="d-none d-md-table-cell align-middle text-muted small">—</td>
                        <td class="text-right align-middle" style="white-space:nowrap;">
                            <a href="<?= base_url('viaggi/' . $v['id']) ?>"
                               class="btn btn-sm btn-outline-primary mr-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="post" action="<?= base_url('viaggi/' . $v['id'] . '/annulla') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Eliminare il viaggio di <?= esc(addslashes($v['cognome'] . ' ' . $v['nome'])) ?>?\nGli interventi torneranno in coda.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        title="Elimina e ripristina interventi">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($approvati)): ?>
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-check-circle mr-1"></i> Approvati
            </h3>
            <div class="card-tools">
                <span class="badge badge-success"><?= count($approvati) ?></span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Tecnico</th>
                        <th>Veicolo</th>
                        <th class="d-none d-md-table-cell">Tappe</th>
                        <th class="d-none d-md-table-cell">Distanza</th>
                        <th>Stato</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($approvati as $v): ?>
                    <?php $s = $stati[$v['stato']] ?? ['label' => $v['stato'], 'badge' => 'badge-secondary']; ?>
                    <tr>
                        <td class="align-middle">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                                background:<?= esc($v['colore'] ?? '#6c757d') ?>;margin-right:6px;"></span>
                            <?= esc($v['cognome'] . ' ' . $v['nome']) ?>
                        </td>
                        <td class="align-middle">
                            <?= $v['veicolo_nome'] ? esc($v['veicolo_nome'] . ($v['veicolo_targa'] ? ' - ' . $v['veicolo_targa'] : '')) : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td class="d-none d-md-table-cell align-middle text-muted small">
                            <?= (int)$v['tappe_count'] > 0 ? (int)$v['tappe_count'] : '—' ?>
                        </td>
                        <td class="d-none d-md-table-cell align-middle text-muted small">
                            <?= $v['distanza_totale'] ? number_format($v['distanza_totale'] / 1000, 1) . ' km' : '—' ?>
                        </td>
                        <td class="align-middle">
                            <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
                        </td>
                        <td class="text-right align-middle" style="white-space:nowrap;">
                            <a href="<?= base_url('viaggi/' . $v['id']) ?>"
                               class="btn btn-sm btn-outline-primary mr-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="post" action="<?= base_url('viaggi/' . $v['id'] . '/annulla') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Eliminare il viaggio di <?= esc(addslashes($v['cognome'] . ' ' . $v['nome'])) ?>?\nGli interventi torneranno in coda.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        title="Elimina e ripristina interventi">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/viaggi/index') ?>
<?= $this->endSection() ?>
