<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('sistema/tecnici') ?>">Tecnici</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('sistema/tecnici/' . $tecnico->id) ?>"><?= esc($tecnico->cognome . ' ' . $tecnico->nome) ?></a></li>
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
            <form method="post" action="<?= base_url('sistema/tecnici/' . $tecnico->id) ?>">
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
                        <div class="form-group col-md-4">
                            <label>Telefono</label>
                            <input type="tel" name="telefono" class="form-control"
                                   value="<?= esc(old('telefono', $tecnico->telefono)) ?>"
                                   placeholder="es. 348 1234567">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Colore</label>
                            <?php
                                $coloriPreset = ['#93c5fd','#fca5a5','#6ee7b7','#fcd34d','#c4b5fd','#f9a8d4','#67e8f9','#fdba74','#5eead4','#a5b4fc','#bef264','#cbd5e1'];
                                $coloreSelezionato = old('colore', $tecnico->colore ?? '#93c5fd');
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
                                   class="form-control" minlength="8"
                                   autocomplete="new-password">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Conferma Password</label>
                            <input type="password" name="password_confirm"
                                   class="form-control" minlength="8"
                                   autocomplete="new-password">
                        </div>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('sistema/tecnici/' . $tecnico->id) ?>" class="btn btn-secondary float-left">
                        <i class="fas fa-times mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-save mr-1"></i> Salva modifiche
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
.color-swatch.is-selected::after { content:'✓'; color:#222; font-size:14px; font-weight:700; }
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

<?= $this->section('help') ?>
<?= $this->include('help/tecnici/edit') ?>
<?= $this->endSection() ?>
