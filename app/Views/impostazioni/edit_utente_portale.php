<?= $this->extend('layouts/admin') ?>

<?= $this->section('help') ?>
<?= $this->include('help/impostazioni/edit_utente_portale') ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni/utenti-portale') ?>">Utenti Portale</a></li>
    <li class="breadcrumb-item active">Modifica</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-1"></i>
                    <?= esc($cliente) ?>
                </h3>
            </div>
            <div class="card-body">

                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 pl-3">
                            <?php foreach (session('errors') as $e): ?>
                                <li><?= esc($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('impostazioni/utenti-portale/' . $utente->id) ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name = "username" class="form-control" value="<?= esc($utente->username) ?>">
                    </div>

                    <div class="form-group">
                        <label for="ruolo">Ruolo <span class="text-danger">*</span></label>
                        <input type="hidden" name="ruolo" value="cliente">
                        <select name="ruolo" id="ruolo" class="form-control" disabled>
                            <option value="<?= 'cliente' ?>">
                                Cliente
                            </option>
                        </select>
                    </div>

                    <hr>
                    <p class="text-muted small">Lascia vuoto per non modificare la password.</p>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="password">Nuova password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control" minlength="8" autocomplete="new-password">
                        </div>
                        <div class="form-group col-6">
                            <label for="password_confirm">Conferma</label>
                            <input type="password" name="password_confirm" id="password_confirm"
                                   class="form-control" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <a href="<?= base_url('impostazioni/utenti-portale') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Annulla
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Salva modifiche
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

