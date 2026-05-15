<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('sistema/tecnici') ?>">Tecnici</a></li>
    <li class="breadcrumb-item active">Nuovo Tecnico</li>
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
                <h3 class="card-title">Dati tecnico</h3>
            </div>
            <form method="post" action="<?= base_url('sistema/tecnici') ?>">
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

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control"
                                   value="<?= esc(old('username')) ?>"
                                   placeholder="es. mario.rossi" required minlength="3" maxlength="30">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Telefono</label>
                            <input type="tel" name="telefono" class="form-control"
                                   value="<?= esc(old('telefono')) ?>"
                                   placeholder="es. 348 1234567">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Colore</label>
                            <input type="color" name="colore" class="form-control form-control-color w-100"
                                   value="<?= esc(old('colore', '#3b82f6')) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                       class="form-control" required minlength="8">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="togglePwd" style="cursor:pointer;">
                                        <i class="fas fa-eye" id="togglePwdIcon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Conferma Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirm"
                                   class="form-control" required minlength="8">
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('sistema/tecnici') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salva Tecnico
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('togglePwd').addEventListener('click', function () {
    var pwd  = document.getElementById('password');
    var icon = document.getElementById('togglePwdIcon');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
});
</script>
<?= $this->endSection() ?>
