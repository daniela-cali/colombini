<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Veicoli Aziendali</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Veicoli aziendali</h3>
        <div class="card-tools">
            <a href="<?= base_url('sistema/veicoli/new') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Nuovo veicolo
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($veicoli)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-truck fa-3x mb-3"></i>
                <p>Nessun veicolo configurato.</p>
                <a href="<?= base_url('sistema/veicoli/new') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Aggiungi veicolo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Targa</th>
                            <th>Stato</th>
                            <th class="text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($veicoli as $v): ?>
                        <tr>
                            <td><?= esc($v['nome']) ?></td>
                            <td><code><?= esc($v['targa']) ?></code></td>
                            <td>
                                <?php if ($v['attivo']): ?>
                                    <span class="badge badge-success">Attivo</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Disattivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <a href="<?= base_url('sistema/veicoli/' . $v['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-outline-primary" title="Modifica">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="post"
                                      action="<?= base_url('sistema/veicoli/' . $v['id'] . '/elimina') ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare il veicolo «<?= esc($v['nome']) ?>»?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
<?= $this->include('help/sistema/veicoli/index') ?>
<?= $this->endSection() ?>
