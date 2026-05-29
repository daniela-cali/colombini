<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni/utenti-app') ?>">Utenti App</a></li>
    <li class="breadcrumb-item active">Nuovo utente</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-plus mr-1"></i> Nuovo utente app</h3>
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

                <form method="post" action="<?= base_url('impostazioni/utenti-app') ?>">
                    <?= csrf_field() ?>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="nome">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control"
                                   value="<?= esc(old('nome')) ?>" required maxlength="100">
                        </div>
                        <div class="form-group col-6">
                            <label for="cognome">Cognome <span class="text-danger">*</span></label>
                            <input type="text" name="cognome" id="cognome" class="form-control"
                                   value="<?= esc(old('cognome')) ?>" required maxlength="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ruolo">Ruolo <span class="text-danger">*</span></label>
                        <select name="ruolo" id="ruolo" class="form-control" required>
                            <option value="">— seleziona —</option>
                            <?php foreach (\App\Models\UserModel::RUOLI as $key => $label):
                                if ($key === 'cliente') continue; ?>
                                <option value="<?= $key ?>" <?= old('ruolo') === $key ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control"
                               value="<?= esc(old('username')) ?>" required
                               minlength="3" maxlength="30" autocomplete="off">
                        <small class="text-muted">
                            L'email di accesso sarà <em>username@gestionale.colombini-snc.it</em>
                        </small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control"
                                   required minlength="8" autocomplete="new-password">
                        </div>
                        <div class="form-group col-6">
                            <label for="password_confirm">Conferma password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirm" id="password_confirm"
                                   class="form-control" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <a href="<?= base_url('impostazioni/utenti-app') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Annulla
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Crea utente
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
