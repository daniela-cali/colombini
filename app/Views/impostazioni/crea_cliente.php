<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni/utenti') ?>">Utenti Portale</a></li>
    <li class="breadcrumb-item active">Nuovo Utente</li>
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
                <h3 class="card-title">Dati accesso portale cliente</h3>
            </div>
            <form method="post" action="<?= base_url('impostazioni/utenti') ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control"
                                   value="<?= esc(old('nome')) ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Cognome <span class="text-danger">*</span></label>
                            <input type="text" name="cognome" class="form-control"
                                   value="<?= esc(old('cognome')) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control"
                               value="<?= esc(old('username')) ?>"
                               placeholder="es. mario.rossi"
                               required minlength="3" maxlength="30">
                        <small class="form-text text-muted">
                            Il cliente userà questo nome per accedere al portale.
                        </small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password"
                                   class="form-control" required minlength="8">
                            <small class="form-text text-muted">Minimo 8 caratteri.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Conferma Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirm"
                                   class="form-control" required minlength="8">
                        </div>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('impostazioni/utenti') ?>" class="btn btn-secondary float-left">
                        <i class="fas fa-arrow-left mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-save mr-1"></i> Crea Utente
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

