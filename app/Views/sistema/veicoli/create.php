<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('sistema/veicoli') ?>">Veicoli Aziendali</a></li>
    <li class="breadcrumb-item active">Nuovo Veicolo</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">

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
                <h3 class="card-title">Nuovo veicolo aziendale</h3>
            </div>
            <form method="post" action="<?= base_url('sistema/veicoli') ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-group">
                        <label>Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control"
                               value="<?= esc(old('nome')) ?>"
                               placeholder="es. Furgone bianco" required maxlength="100">
                    </div>

                    <div class="form-group">
                        <label>Targa <span class="text-danger">*</span></label>
                        <input type="text" name="targa" class="form-control text-uppercase"
                               value="<?= esc(old('targa')) ?>"
                               placeholder="es. AB123CD" required maxlength="20"
                               style="text-transform:uppercase;">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Carico massimo (kg)</label>
                            <input type="number" name="carico_massimo" class="form-control"
                                   value="<?= esc(old('carico_massimo')) ?>"
                                   min="0" placeholder="es. 800 — lascia vuoto se non rilevante">
                        </div>
                        <div class="form-group col-md-6 d-flex align-items-end pb-2">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="cambio_automatico" value="0">
                                <input type="checkbox" class="custom-control-input" id="cambio_automatico"
                                       name="cambio_automatico" value="1"
                                       <?= old('cambio_automatico') ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="cambio_automatico">Cambio automatico</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('sistema/veicoli') ?>" class="btn btn-secondary float-left">
                        <i class="fas fa-arrow-left mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-save mr-1"></i> Salva
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/sistema/veicoli/create') ?>
<?= $this->endSection() ?>
