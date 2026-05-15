<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti') ?>">Clienti</a></li>
    <li class="breadcrumb-item active">Import CSV</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-csv mr-1"></i> Import Clienti da CSV</h3>
            </div>
            <form method="post" action="<?= base_url('clienti/import') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Carica un file <strong>.csv</strong> con qualsiasi struttura — nel passo successivo
                        potrai mappare le colonne sui campi del gestionale.<br>
                        Separatori supportati: <code>;</code> e <code>,</code> — encoding: UTF-8 o ISO-8859-1.
                    </div>

                    <div class="form-group">
                        <label for="csv_file">File CSV <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" name="csv_file" id="csv_file"
                                   class="custom-file-input" accept=".csv,.txt" required>
                            <label class="custom-file-label" for="csv_file">Scegli file...</label>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-sync-alt mr-1"></i>
                        I clienti già presenti (stesso <strong>codice</strong>) verranno
                        <strong>aggiornati</strong>, non duplicati.
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('clienti') ?>" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right mr-1"></i> Carica e continua
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('csv_file').addEventListener('change', function () {
    var label = this.nextElementSibling;
    label.textContent = this.files[0] ? this.files[0].name : 'Scegli file...';
});
</script>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<h5><i class="fas fa-file-csv mr-1"></i> Formato del file</h5>
<p>Carica un file <strong>.csv</strong> con separatore <code>;</code> o <code>,</code> e encoding UTF-8 o ISO-8859-1. Non è necessaria una struttura predefinita: nel passo successivo potrai associare ogni colonna al campo corretto.</p>
<h5><i class="fas fa-sync-alt mr-1"></i> Aggiornamento vs. inserimento</h5>
<p>I clienti già presenti con lo stesso <strong>codice</strong> vengono aggiornati, non duplicati. I clienti senza codice corrispondente vengono creati come nuovi.</p>
<h5><i class="fas fa-map-marker-alt mr-1"></i> Geocodifica dopo l'import</h5>
<p>Al termine dell'import gli indirizzi non vengono geocodificati automaticamente. Esegui <code>php spark geocode:clienti</code> da terminale per rilevare le coordinate di tutti i clienti importati.</p>
<p><span class="badge-tip"><i class="fas fa-lightbulb mr-1"></i> Il sistema memorizza il mapping colonne dell'ultimo import: la prossima volta i campi saranno già precompilati.</span></p>
<?= $this->endSection() ?>
