<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active"><?= esc($page_title ?? 'Sezione') ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-4">
    <div class="col-md-6 text-center">
        <div class="card card-outline card-primary">
            <div class="card-body py-5">
                <i class="fas fa-tools fa-4x mb-4" style="color: var(--clr-teal);"></i>
                <h2 class="mb-2">In costruzione</h2>
                <p class="text-muted mb-4">
                    La sezione <strong><?= esc($page_title ?? 'questa sezione') ?></strong>
                    è in fase di sviluppo e sarà disponibile a breve.
                </p>
                <a href="<?= base_url('/') ?>" class="btn btn-primary">
                    <i class="fas fa-home mr-1"></i> Torna alla Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
