<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('sistema/tipi-intervento') ?>">Tipi Intervento</a></li>
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
                <h3 class="card-title">Modifica tipo di intervento</h3>
            </div>
            <form method="post" action="<?= base_url('sistema/tipi-intervento/' . $tipo['id']) ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-group">
                        <label>Codice <span class="text-danger">*</span></label>
                        <input type="text" name="codice" class="form-control"
                               value="<?= esc(old('codice', $tipo['codice'])) ?>"
                               required maxlength="50">
                        <small class="text-muted">Usato come chiave negli interventi esistenti — modificare con cautela.</small>
                    </div>

                    <div class="form-group">
                        <label>Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control"
                               value="<?= esc(old('nome', $tipo['nome'])) ?>"
                               required maxlength="100">
                    </div>

                    <?php $this->setVar('valoreIcona', old('icona', $tipo['icona'] ?? 'fa-tools')); ?>
                    <?= $this->include('sistema/tipi_intervento/_icona_picker') ?>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Durata default (minuti) <span class="text-danger">*</span></label>
                            <input type="number" name="durata_default" class="form-control"
                                   value="<?= esc(old('durata_default', $tipo['durata_default'])) ?>"
                                   min="1" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Ordine</label>
                            <input type="number" name="ordine" class="form-control"
                                   value="<?= esc(old('ordine', $tipo['ordine'])) ?>"
                                   min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="attivo" value="0">
                            <input type="checkbox" class="custom-control-input" id="attivo" name="attivo" value="1"
                                   <?= old('attivo', $tipo['attivo']) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="attivo">Tipo attivo</label>
                        </div>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('sistema/tipi-intervento') ?>" class="btn btn-secondary float-left">
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
<?= $this->include('help/sistema/tipi_intervento/edit') ?>
<?= $this->endSection() ?>

