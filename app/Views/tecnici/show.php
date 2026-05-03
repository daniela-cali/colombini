<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tecnici') ?>">Tecnici</a></li>
    <li class="breadcrumb-item active"><?= esc($tecnico->cognome . ' ' . $tecnico->nome) ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">

    <!-- Scheda anagrafica -->
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-body text-center pt-4">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x" style="color: var(--clr-teal);"></i>
                </div>
                <h4 class="mb-0"><?= esc($tecnico->nome . ' ' . $tecnico->cognome) ?></h4>
                <p class="text-muted mb-3">@<?= esc($tecnico->username) ?></p>
                <?php if ($tecnico->telefono): ?>
                    <p><i class="fas fa-phone mr-1 text-muted"></i>
                        <a href="tel:<?= esc($tecnico->telefono) ?>"><?= esc($tecnico->telefono) ?></a>
                    </p>
                <?php endif; ?>
                <span class="badge badge-info px-3 py-1">Tecnico</span>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="<?= base_url('tecnici') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Elenco
                </a>
                <div>
                    <a href="<?= base_url('tecnici/' . $tecnico->id . '/edit') ?>"
                       class="btn btn-sm btn-outline-primary mr-1">
                        <i class="fas fa-edit mr-1"></i> Modifica
                    </a>
                    <a href="<?= base_url('interventi/new?tecnico_id=' . $tecnico->id) ?>"
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Nuovo intervento
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Interventi assegnati -->
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Interventi assegnati</h3>
                <div class="card-tools">
                    <span class="badge badge-primary"><?= count($interventi) ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($interventi)): ?>
                    <div class="text-center py-4 text-muted">
                        <p>Nessun intervento assegnato.</p>
                        <a href="<?= base_url('interventi/new?tecnico_id=' . $tecnico->id) ?>"
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-plus mr-1"></i> Assegna intervento
                        </a>
                    </div>
                <?php else: ?>
                    <table class="table table-hover mb-0 table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Stato</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($interventi as $inv): ?>
                            <tr>
                                <td class="text-muted"><?= $inv['id'] ?></td>
                                <td><?= esc($tipi[$inv['tipo_intervento']] ?? $inv['tipo_intervento']) ?></td>
                                <td><?= $inv['cliente_nome'] ? esc($inv['cliente_cognome'] . ' ' . $inv['cliente_nome']) : '—' ?></td>
                                <td class="small text-muted">
                                    <?= $inv['data_pianificata'] ? date('d/m/Y', strtotime($inv['data_pianificata'])) : '—' ?>
                                </td>
                                <td>
                                    <?php $s = $stati[$inv['stato']] ?? ['label' => $inv['stato'], 'badge' => 'badge-secondary']; ?>
                                    <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('interventi/' . $inv['id']) ?>"
                                       class="btn btn-xs btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
