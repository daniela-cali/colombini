<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('sistema/veicoli') ?>">Veicoli Aziendali</a></li>
    <li class="breadcrumb-item active">Modifica</li>
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
                <h3 class="card-title">Modifica veicolo aziendale</h3>
            </div>
            <form method="post" action="<?= base_url('sistema/veicoli/' . $veicolo['id']) ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-group">
                        <label>Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control"
                               value="<?= esc(old('nome', $veicolo['nome'])) ?>"
                               required maxlength="100">
                    </div>

                    <div class="form-group">
                        <label>Targa <span class="text-danger">*</span></label>
                        <input type="text" name="targa" class="form-control"
                               value="<?= esc(old('targa', $veicolo['targa'])) ?>"
                               required maxlength="20"
                               style="text-transform:uppercase;">
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="attivo" value="0">
                            <input type="checkbox" class="custom-control-input" id="attivo" name="attivo" value="1"
                                   <?= old('attivo', $veicolo['attivo']) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="attivo">Veicolo attivo</label>
                        </div>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('sistema/veicoli') ?>" class="btn btn-secondary float-left">
                        <i class="fas fa-arrow-left mr-1"></i> Annulla
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

<?= $this->section('help') ?>
<?= $this->include('help/sistema/veicoli/edit') ?>
<?= $this->endSection() ?>
