<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item active">Utenti Portale</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Clienti con accesso al portale</h3>
                <a href="<?= base_url('impostazioni/utenti/new') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Nuovo utente
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($clienti)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>Nessun utente portale ancora creato.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Stato</th>
                                <th class="text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($clienti as $c): ?>
                            <tr>
                                <td><i class="fas fa-user-circle mr-1 text-muted"></i> <?= esc($c->username) ?></td>
                                <td><?= esc($c->nome) ?></td>
                                <td><?= esc($c->cognome) ?></td>
                                <td>
                                    <?php if ($c->active): ?>
                                        <span class="badge badge-success">Attivo</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Disattivato</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <form method="post"
                                          action="<?= base_url('impostazioni/utenti/' . $c->id . '/elimina') ?>"
                                          onsubmit="return confirm('Eliminare l\'utente <?= esc($c->username) ?>?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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

<?= $this->section('help') ?>
<?= $this->include('help/impostazioni/utenti') ?>
<?= $this->endSection() ?>
