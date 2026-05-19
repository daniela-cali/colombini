<?= $this->extend('layouts/admin') ?>

<?= $this->section('help') ?>
<?= $this->include('help/impostazioni/edit_utente_app') ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni/utenti-app') ?>">Utenti App</a></li>
    <li class="breadcrumb-item active">Modifica</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-1"></i>
                    <?= esc($utente->cognome . ' ' . $utente->nome) ?>
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

                <form method="post" action="<?= base_url('impostazioni/utenti-app/' . $utente->id) ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" value="<?= esc($utente->username) ?>" disabled>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="nome">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control"
                                   value="<?= esc(old('nome', $utente->nome)) ?>" required maxlength="100">
                        </div>
                        <div class="form-group col-6">
                            <label for="cognome">Cognome <span class="text-danger">*</span></label>
                            <input type="text" name="cognome" id="cognome" class="form-control"
                                   value="<?= esc(old('cognome', $utente->cognome)) ?>" required maxlength="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ruolo">Ruolo <span class="text-danger">*</span></label>
                        <select name="ruolo" id="ruolo" class="form-control" required>
                            <?php foreach (\App\Models\UserModel::RUOLI as $key => $label):
                                if ($key === 'cliente') continue; ?>
                                <option value="<?= $key ?>"
                                    <?= old('ruolo', $utente->ruolo) === $key ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <?php if ($utente->ruolo === 'tecnico'): ?>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle mr-1"></i>
                                I tecnici sono sempre assegnabili agli interventi.
                            </div>
                        <?php else: ?>
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="assegnabile_interventi" value="0">
                                <input type="checkbox" class="custom-control-input" id="assegnabile_interventi"
                                       name="assegnabile_interventi" value="1"
                                       <?= old('assegnabile_interventi', $utente->assegnabile_interventi) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="assegnabile_interventi">
                                    Assegnabile agli interventi
                                </label>
                                <small class="form-text text-muted">
                                    Questo utente compare nelle liste tecnici e può essere assegnato agli interventi.
                                </small>
                            </div>
                        <?php endif; ?>
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
                        <a href="<?= base_url('impostazioni/utenti-app') ?>" class="btn btn-secondary">
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

