<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Impostazioni</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">

    <div class="col-md-4">
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

    <div class="col-md-4">
        <a href="<?= base_url('impostazioni/utenti') ?>" class="text-decoration-none">
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

    <div class="col-md-4">
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

    <div class="col-md-4">
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
<h5><i class="fas fa-cog mr-1"></i> Sezioni disponibili</h5>
<p>Le impostazioni sono suddivise in aree tematiche. Clicca su una scheda per accedere alla configurazione corrispondente.</p>
<h5><i class="fas fa-users-cog mr-1"></i> Utenti App</h5>
<p>Gestisci gli account con accesso al gestionale: amministratori, staff e tecnici. Da qui puoi creare nuovi utenti, modificare ruolo e password.</p>
<h5><i class="fas fa-users mr-1"></i> Utenti Portale</h5>
<p>Crea e gestisci i clienti che possono accedere al portale di assistenza per inviare richieste di intervento.</p>
<h5><i class="fas fa-sliders-h mr-1"></i> Parametri Generali</h5>
<p>Configura i dati aziendali (sede, contatti), gli orari di lavoro predefiniti per i tecnici e le durate standard degli interventi.</p>
<?= $this->endSection() ?>
