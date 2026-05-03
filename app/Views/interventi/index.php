<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Interventi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Elenco interventi</h3>
        <a href="<?= base_url('interventi/new') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline">Nuovo intervento</span>
            <span class="d-sm-none">Nuovo</span>
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($interventi)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                <p>Nessun intervento ancora registrato.</p>
                <a href="<?= base_url('interventi/new') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Crea il primo intervento
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">#</th>
                            <th>Tipo</th>
                            <th class="d-none d-lg-table-cell">Cliente</th>
                            <th class="d-none d-md-table-cell">Tecnico</th>
                            <th>Data</th>
                            <th>Stato</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($interventi as $inv): ?>
                        <tr>
                            <td class="d-none d-md-table-cell text-muted small"><?= $inv['id'] ?></td>
                            <td>
                                <strong><?= esc($tipi[$inv['tipo_intervento']] ?? $inv['tipo_intervento']) ?></strong>
                                <?php if ($inv['luogo_intervento']): ?>
                                    <br><small class="text-muted d-md-none"><?= esc($inv['luogo_intervento']) ?></small>
                                <?php endif; ?>
                                <?php if ($inv['tecnico_nome']): ?>
                                    <br><small class="text-muted d-md-none">
                                        <i class="fas fa-user-hard-hat fa-xs"></i>
                                        <?= esc($inv['tecnico_cognome'] . ' ' . $inv['tecnico_nome']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <?= $inv['cliente_nome']
                                    ? esc($inv['cliente_cognome'] . ' ' . $inv['cliente_nome'])
                                    : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <?= $inv['tecnico_nome']
                                    ? esc($inv['tecnico_cognome'] . ' ' . $inv['tecnico_nome'])
                                    : '<span class="text-muted small">Non assegnato</span>' ?>
                            </td>
                            <td class="small">
                                <?php if ($inv['data_pianificata']): ?>
                                    <span class="d-none d-sm-inline">
                                        <?= date('d/m/Y H:i', strtotime($inv['data_pianificata'])) ?>
                                    </span>
                                    <span class="d-sm-none">
                                        <?= date('d/m', strtotime($inv['data_pianificata'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $s = $stati[$inv['stato']] ?? ['label' => $inv['stato'], 'badge' => 'badge-secondary']; ?>
                                <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
                            </td>
                            <td>
                                <a href="<?= base_url('interventi/' . $inv['id']) ?>"
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