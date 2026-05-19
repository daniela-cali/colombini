<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Ottimizzazione Viaggi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-outline card-primary text-center py-5">
            <div class="card-body">
                <i class="fas fa-route fa-4x text-primary mb-4" style="opacity:.3;"></i>
                <h3 class="mb-2">Ottimizzazione automatica dei percorsi</h3>
                <p class="text-muted mb-4" style="max-width:480px; margin:0 auto 1.5rem;">
                    Questa sezione calcolerà automaticamente i percorsi ottimali tramite
                    OpenRouteService, tenendo conto di competenze tecniche, vincoli veicolo
                    e orari di lavoro.
                </p>
                <span class="badge badge-warning px-4 py-2" style="font-size:.9rem;">
                    <i class="fas fa-hammer mr-1"></i> Prossimamente
                </span>
                <div class="mt-4">
                    <a href="<?= base_url('pianificazione') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-alt mr-1"></i> Vai alla pianificazione manuale
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/ottimizzazione/index') ?>
<?= $this->endSection() ?>
