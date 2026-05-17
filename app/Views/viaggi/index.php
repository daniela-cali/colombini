<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Viaggi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title mb-0">Viaggi pianificati</h3>
        <div class="card-tools d-flex align-items-center">
            <form method="get" action="<?= base_url('viaggi') ?>" class="mr-2 mb-0">
                <input type="date" name="data" class="form-control form-control-sm"
                       value="<?= esc($data) ?>"
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($viaggi)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-route fa-3x mb-3"></i>
                <p>Nessun viaggio pianificato per il <strong><?= date('d/m/Y', strtotime($data)) ?></strong>.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
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
                    <?php foreach ($viaggi as $v): ?>
                        <?php $s = $stati[$v['stato']] ?? ['label' => $v['stato'], 'badge' => 'badge-secondary']; ?>
                        <tr>
                            <td class="align-middle">
                                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                                    background:<?= esc($v['colore'] ?? '#6c757d') ?>;margin-right:6px;"></span>
                                <?= esc($v['cognome'] . ' ' . $v['nome']) ?>
                            </td>
                            <td class="align-middle">
                                <?= $v['veicolo_nome'] ? esc($v['veicolo_nome']) : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td class="d-none d-md-table-cell align-middle text-muted small">
                                —
                            </td>
                            <td class="d-none d-md-table-cell align-middle text-muted small">
                                <?= $v['distanza_totale'] ? number_format($v['distanza_totale'] / 1000, 1) . ' km' : '—' ?>
                            </td>
                            <td class="align-middle">
                                <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
                            </td>
                            <td class="text-right align-middle">
                                <a href="<?= base_url('viaggi/' . $v['id']) ?>"
                                   class="btn btn-sm btn-outline-primary">
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

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/viaggi/index') ?>
<?= $this->endSection() ?>
