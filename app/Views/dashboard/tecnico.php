<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item active">La mia agenda</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Widgets contatori -->
<div class="row">
    <div class="col-6 col-lg-3">
        <div class="info-box">
            <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pianificati</span>
                <span class="info-box-number"><?= $stats['pianificati'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-spinner"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">In corso</span>
                <span class="info-box-number"><?= $stats['in_corso'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Completati questo mese</span>
                <span class="info-box-number"><?= $stats['completati_mese'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-tools"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Totale completati</span>
                <span class="info-box-number"><?= $stats['totale_completati'] ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Prossimi interventi -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-check mr-1"></i> Prossimi interventi
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('interventi?tecnico_id=' . $tecnico->id) ?>"
                       class="btn btn-sm btn-outline-primary">
                        Tutti i miei interventi
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($prossimi)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <p class="mb-0">Nessun intervento in programma.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Cliente</th>
                                    <th class="d-none d-md-table-cell">Luogo</th>
                                    <th>Stato</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($prossimi as $inv):
                                $s = $stati[$inv['stato']] ?? ['label' => $inv['stato'], 'badge' => 'badge-secondary'];
                                $cliente = $inv['cliente_ragsoc']
                                    ?: trim(($inv['cliente_cognome'] ?? '') . ' ' . ($inv['cliente_nome'] ?? ''));
                            ?>
                                <tr>
                                    <td class="align-middle text-nowrap">
                                        <?php if ($inv['data_pianificata']):
                                            $ts = strtotime($inv['data_pianificata']);
                                            $ora = date('H:i', $ts);
                                        ?>
                                            <strong><?= date('d/m', $ts) ?></strong>
                                            <span class="text-muted small"><?= date('Y', $ts) ?></span>
                                            <?php if ($ora !== '00:00'): ?>
                                                <br><span class="text-primary small"><i class="fas fa-clock mr-1"></i><?= $ora ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <i class="fas <?= esc($icone[$inv['tipo_intervento']] ?? 'fa-tools') ?> mr-1 text-muted"></i><?= esc($tipi[$inv['tipo_intervento']] ?? $inv['tipo_intervento']) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= $cliente ? esc($cliente) : '<span class="text-muted">—</span>' ?>
                                    </td>
                                    <td class="d-none d-md-table-cell align-middle text-muted small">
                                        <?= esc($inv['luogo_intervento'] ?? '—') ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
                                    </td>
                                    <td class="align-middle text-right">
                                        <a href="<?= base_url('interventi/' . $inv['id']) ?>"
                                           class="btn btn-xs btn-outline-primary">
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
        </div>
    </div>
</div>

<!-- Placeholder interventi nelle vicinanze -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-secondary">
            <div class="card-body text-center py-4 text-muted">
                <i class="fas fa-map-marked-alt fa-2x mb-2"></i>
                <p class="mb-0">
                    <strong>Interventi nelle vicinanze</strong> — disponibile nella prossima versione.
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/dashboard/tecnico') ?>
<?= $this->endSection() ?>
