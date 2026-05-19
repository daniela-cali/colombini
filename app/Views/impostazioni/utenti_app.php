<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item active">Utenti App</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Utenti con accesso al gestionale</h3>
                <div class="card-tools">
                    <a href="<?= base_url('impostazioni/utenti-app/new') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Nuovo utente
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php
                $badgeRuolo = [
                    'admin'   => 'badge-danger',
                    'staff'   => 'badge-info',
                    'tecnico' => 'badge-primary',
                ];
                ?>
                <?php if (empty($utenti)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-users-cog fa-3x mb-3"></i>
                        <p>Nessun utente app ancora configurato.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nominativo</th>
                                <th>Ruolo</th>
                                <th>Stato</th>
                                <th class="text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($utenti as $u): ?>
                            <tr>
                                <td class="align-middle">
                                    <i class="fas fa-user-circle mr-1 text-muted"></i>
                                    <?= esc($u->username) ?>
                                </td>
                                <td class="align-middle">
                                    <?= esc(trim($u->cognome . ' ' . $u->nome)) ?: '—' ?>
                                </td>
                                <td class="align-middle">
                                    <span class="badge <?= $badgeRuolo[$u->ruolo] ?? 'badge-secondary' ?>">
                                        <?= esc(\App\Models\UserModel::RUOLI[$u->ruolo] ?? $u->ruolo) ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <?php if ($u->active): ?>
                                        <span class="badge badge-success">Attivo</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Disattivato</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right align-middle">
                                    <?php if ($u->ruolo === 'tecnico' || $u->assegnabile_interventi): ?>
                                        <a href="<?= base_url('sistema/tecnici/' . $u->id) ?>"
                                           class="btn btn-sm btn-outline-primary mr-1" title="Scheda tecnico">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($u->ruolo !== 'tecnico'): ?>
                                        <a href="<?= base_url('impostazioni/utenti-app/' . $u->id . '/edit') ?>"
                                           class="btn btn-sm btn-outline-secondary mr-1" title="Modifica">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="post"
                                              action="<?= base_url('impostazioni/utenti-app/' . $u->id . '/elimina') ?>"
                                              onsubmit="return confirm('Eliminare l\'utente <?= esc($u->username) ?>?')"
                                              class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
