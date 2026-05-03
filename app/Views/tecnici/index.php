<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Tecnici</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Elenco tecnici</h3>
        <a href="<?= base_url('tecnici/new') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline">Nuovo tecnico</span>
            <span class="d-sm-none">Nuovo</span>
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($tecnici)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-hard-hat fa-3x mb-3"></i>
                <p>Nessun tecnico ancora inserito.</p>
                <a href="<?= base_url('tecnici/new') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Aggiungi tecnico
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th class="d-none d-sm-table-cell">Username</th>
                            <th class="d-none d-md-table-cell">Telefono</th>
                            <th class="text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tecnici as $t): ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('tecnici/' . $t->id) ?>">
                                    <?= esc($t->cognome . ' ' . $t->nome) ?>
                                </a>
                                <?php if ($t->telefono): ?>
                                    <br><small class="text-muted d-md-none">
                                        <i class="fas fa-phone fa-xs"></i> <?= esc($t->telefono) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted d-none d-sm-table-cell"><?= esc($t->username) ?></td>
                            <td class="d-none d-md-table-cell"><?= esc($t->telefono ?? '—') ?></td>
                            <td class="text-right">
                                <a href="<?= base_url('tecnici/' . $t->id) ?>"
                                   class="btn btn-sm btn-outline-primary" title="Scheda">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="post"
                                      action="<?= base_url('tecnici/' . $t->id . '/elimina') ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare il tecnico <?= esc($t->cognome) ?>?')">
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
