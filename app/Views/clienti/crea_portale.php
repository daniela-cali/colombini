<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti') ?>">Clienti</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti/' . $cliente['id']) ?>"><?= esc($nome_display) ?></a></li>
    <li class="breadcrumb-item active">Crea Accesso Portale</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">

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
                <h3 class="card-title"><i class="fas fa-globe mr-2"></i>Crea Accesso Portale</h3>
            </div>
            <div class="card-body">

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i>
                    Stai creando le credenziali di accesso al portale clienti per
                    <strong><?= esc($nome_display) ?></strong>.
                </div>

                <form method="post" action="<?= base_url('clienti/' . $cliente['id'] . '/portale') ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control"
                               minlength="3" maxlength="30" required
                               value="<?= esc(old('username', $username_suggerito)) ?>"
                               placeholder="es. rossi.mario">
                        <small class="form-text text-muted">
                            Minimo 3 caratteri, solo lettere, numeri e punto/trattino.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control"
                               minlength="8" required
                               placeholder="Minimo 8 caratteri">
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Conferma password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirm" id="password_confirm"
                               class="form-control" minlength="8" required
                               placeholder="Ripeti la password">
                    </div>

                    <div class="card-footer px-0 pb-0 d-flex justify-content-between">
                        <a href="<?= base_url('clienti/' . $cliente['id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Annulla
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key mr-1"></i> Crea accesso
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

