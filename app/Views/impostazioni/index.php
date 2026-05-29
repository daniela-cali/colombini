<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Impostazioni</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">

    <div class="col-md-4 mb-4">
        <a href="<?= base_url('impostazioni/utenti-app') ?>" class="text-decoration-none">
            <div class="card card-outline card-primary h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-users-cog fa-3x mb-3" style="color: var(--clr-teal);"></i>
                    <h5 class="card-title">Utenti App</h5>
                    <p class="text-muted small mb-0">
                        Amministratori, staff e tecnici con accesso al gestionale.
                    </p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 mb-4">
        <a href="<?= base_url('impostazioni/utenti-portale') ?>" class="text-decoration-none">
            <div class="card card-outline card-primary h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-users fa-3x mb-3" style="color: var(--clr-teal);"></i>
                    <h5 class="card-title">Utenti Portale</h5>
                    <p class="text-muted small mb-0">
                        Crea e gestisci i clienti con accesso al portale di assistenza.
                    </p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 mb-4">
        <a href="<?= base_url('impostazioni/parametri') ?>" class="text-decoration-none">
            <div class="card card-outline card-primary h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-sliders-h fa-3x mb-3" style="color: var(--clr-teal);"></i>
                    <h5 class="card-title">Parametri Generali</h5>
                    <p class="text-muted small mb-0">
                        Sede, orari default tecnici e durate standard degli interventi.
                    </p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 mb-4">
        <a href="<?= base_url('impostazioni/geocodifica') ?>" class="text-decoration-none">
            <div class="card card-outline card-primary h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-map-marked-alt fa-3x mb-3" style="color: var(--clr-teal);"></i>
                    <h5 class="card-title">Geocodifica Clienti</h5>
                    <p class="text-muted small mb-0">
                        Verifica e aggiorna le coordinate geografiche dei clienti per l'ottimizzazione dei percorsi.
                    </p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card card-outline card-secondary h-100">
            <div class="card-body text-center py-4 text-muted">
                <i class="fas fa-envelope fa-3x mb-3"></i>
                <h5 class="card-title">Notifiche Email</h5>
                <p class="small mb-0">In costruzione.</p>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/impostazioni/index') ?>
<?= $this->endSection() ?>
