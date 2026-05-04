<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tecnici') ?>">Tecnici</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tecnici/' . $tecnico->id) ?>"><?= esc($tecnico->cognome . ' ' . $tecnico->nome) ?></a></li>
    <li class="breadcrumb-item active">Modifica</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7">

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Modifica Tecnico</h3>
            </div>
            <form method="post" action="<?= base_url('tecnici/' . $tecnico->id) ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control"
                                   value="<?= esc(old('nome', $tecnico->nome)) ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Cognome <span class="text-danger">*</span></label>
                            <input type="text" name="cognome" class="form-control"
                                   value="<?= esc(old('cognome', $tecnico->cognome)) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username</label>
                            <input type="text" class="form-control" value="<?= esc($tecnico->username) ?>" disabled>
                            <small class="form-text text-muted">Lo username non può essere modificato.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Telefono</label>
                            <input type="tel" name="telefono" class="form-control"
                                   value="<?= esc(old('telefono', $tecnico->telefono)) ?>"
                                   placeholder="es. 348 1234567">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ruolo">Ruolo <span class="text-danger">*</span></label>
                        <select name="ruolo" id="ruolo" class="form-control" required>
                            <?php foreach (\App\Models\UserModel::RUOLI as $key => $label):
                                if ($key === 'cliente') continue; ?>
                                <option value="<?= $key ?>"
                                    <?= old('ruolo', $tecnico->ruolo) === $key ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr>
                    <p class="text-muted small mb-2">Lascia vuoto per non modificare la password.</p>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nuova Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control" minlength="8">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Conferma Password</label>
                            <input type="password" name="password_confirm"
                                   class="form-control" minlength="8">
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('tecnici/' . $tecnico->id) ?>" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salva modifiche
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

