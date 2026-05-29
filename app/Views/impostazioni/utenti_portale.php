<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item active">Utenti Portale</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Clienti con accesso al portale</h3>
                <div class="card-tools">
                    <a href="<?= base_url('impostazioni/utenti-portale/new') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Nuovo utente portale
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($utenti_portale)): ?>
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
                        <?php foreach ($utenti_portale as $u): ?>
                            <tr>
                                <td><i class="fas fa-user-circle mr-1 text-muted"></i> <?= esc($u->username) ?></td>
                                <td><?= esc($u->nome) ?></td>
                                <td><?= esc($u->cognome) ?></td>
                                <td>
                                    <?php if ($u->active): ?>
                                        <span class="badge badge-success">Attivo</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Disattivato</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right align-middle">
                                    <a href="<?= base_url('impostazioni/utenti-portale/' . $u->id . '/edit') ?>"
                                           class="btn btn-sm btn-outline-secondary mr-1" title="Modifica">
                                            <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="post"
                                        class="d-inline"
                                        action="<?= base_url('impostazioni/utenti-portale/' . $u->id . '/elimina') ?>"
                                        onsubmit="return confirm('Eliminare l\'utente <?= esc($u->username) ?>?')">
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
<?= $this->include('help/impostazioni/utenti_portale') ?>
<?= $this->endSection() ?>
