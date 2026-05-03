<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti') ?>">Clienti</a></li>
    <li class="breadcrumb-item active">Risultato Import</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">

        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check-circle mr-1"></i> Import completato</h3>
            </div>
            <div class="card-body">

                <div class="row text-center">
                    <div class="col-4">
                        <div class="description-block border-right">
                            <h5 class="description-header text-success"><?= $result['totale'] ?? 0 ?></h5>
                            <span class="description-text text-muted">ELABORATI</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="description-block border-right">
                            <h5 class="description-header text-primary"><?= $result['elaborati'] ?? 0 ?></h5>
                            <span class="description-text text-muted">INSERITI / AGGIORNATI</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="description-block">
                            <h5 class="description-header text-warning"><?= $result['saltati'] ?? 0 ?></h5>
                            <span class="description-text text-muted">SALTATI</span>
                        </div>
                    </div>
                </div>

                <?php if ($result['saltati'] > 0): ?>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Le righe saltate non avevano il campo <strong>Codice</strong> valorizzato.
                    </div>
                <?php endif; ?>

            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('clienti/import') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-redo mr-1"></i> Nuovo import
                    </a>
                    <a href="<?= base_url('clienti') ?>" class="btn btn-primary">
                        <i class="fas fa-users mr-1"></i> Vai ai clienti
                    </a>
                </div>
                <div class="mt-3 text-muted small text-center">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Per geocodificare gli indirizzi importati esegui da terminale:
                    <code>php spark geocode:clienti</code>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>