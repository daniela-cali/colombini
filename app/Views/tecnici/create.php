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
                        <div class="form-group col-md-3">
                            <label>Colore</label>
                            <?php
                                $coloriPreset = ['#3b82f6','#ef4444','#10b981','#f59e0b','#8b5cf6','#ec4899','#06b6d4','#f97316','#14b8a6','#6366f1','#84cc16','#64748b'];
                                $coloreSelezionato = old('colore', '#3b82f6');
                            ?>
                            <div class="color-swatches">
                                <?php foreach ($coloriPreset as $c): ?>
                                    <label class="color-swatch<?= $c === $coloreSelezionato ? ' is-selected' : '' ?>"
                                           style="background:<?= $c ?>" title="<?= $c ?>">
                                        <input type="radio" name="colore" value="<?= $c ?>"
                                               <?= $c === $coloreSelezionato ? 'checked' : '' ?>>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password"
                                   class="form-control" required minlength="8">
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

<?= $this->section('styles') ?>
<style>
.color-swatches { display:flex; flex-wrap:wrap; gap:8px; padding-top:4px; }
.color-swatch {
    width:30px; height:30px; border-radius:50%; cursor:pointer;
    border:2px solid transparent; display:flex; align-items:center; justify-content:center;
    transition:transform .1s, box-shadow .1s;
}
.color-swatch input { position:absolute; opacity:0; width:0; height:0; }
.color-swatch:hover { transform:scale(1.15); }
.color-swatch.is-selected {
    box-shadow:0 0 0 2px #fff, 0 0 0 4px rgba(0,0,0,.35);
}
.color-swatch.is-selected::after { content:'✓'; color:#fff; font-size:14px; font-weight:700; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.color-swatch input').forEach(function (r) {
    r.addEventListener('change', function () {
        document.querySelectorAll('.color-swatch').forEach(function (s) { s.classList.remove('is-selected'); });
        this.closest('.color-swatch').classList.add('is-selected');
    });
});

</script>
<?= $this->endSection() ?>
