<?= $this->extend('layouts/portale') ?>

<?= $this->section('content') ?>
<div class="px-2 px-sm-3">

    <div class="mb-3">
        <a href="<?= base_url('portale') ?>" class="text-muted small">
            <i class="fas fa-arrow-left mr-1"></i> Le mie richieste
        </a>
    </div>

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

    <div class="p-card">
        <div class="p-card-header">
            <i class="fas fa-tools"></i>Nuova Richiesta di Assistenza
        </div>
        <form method="post" action="<?= base_url('portale/nuova-richiesta') ?>">
            <?= csrf_field() ?>
            <div class="p-card-body">

                <!-- Tipo intervento -->
                <div class="form-group">
                    <label class="font-weight-bold">
                        Tipo di intervento <span class="text-danger">*</span>
                    </label>
                    <div class="row no-gutters" style="gap:0;">
                        <?php
                        $icone = [
                            'piscine'     => 'fa-swimming-pool',
                            'addolcitori' => 'fa-filter',
                            'acquedotti'  => 'fa-water',
                            'commerciale' => 'fa-handshake',
                        ];
                        foreach ($tipi as $key => $label): ?>
                        <div class="col-6 p-1">
                            <div class="tipo-card <?= old('tipo_intervento') === $key ? 'selected' : '' ?>"
                                 data-value="<?= $key ?>">
                                <i class="fas <?= $icone[$key] ?>"></i>
                                <?= $label ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="tipo_intervento" id="tipo_intervento"
                           value="<?= esc(old('tipo_intervento')) ?>" required>
                </div>

                <!-- Luogo -->
                <div class="form-group">
                    <label>Luogo dell'intervento</label>
                    <input type="text" name="luogo_intervento" class="form-control"
                           value="<?= esc(old('luogo_intervento')) ?>"
                           placeholder="Lasciare vuoto se coincide con la sede">
                </div>

                <!-- Chi richiede -->
                <div class="form-group">
                    <label class="font-weight-bold">
                        Nome di chi richiede <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="richiedente" class="form-control"
                           value="<?= esc(old('richiedente')) ?>"
                           placeholder="Nome e Cognome" required>
                </div>

                <!-- Telefono -->
                <div class="form-group">
                    <label class="font-weight-bold">
                        Telefono di contatto <span class="text-danger">*</span>
                    </label>
                    <input type="tel" name="telefono_contatto" class="form-control"
                           value="<?= esc(old('telefono_contatto')) ?>"
                           placeholder="es. 348 1234567" required>
                </div>

                <!-- Descrizione -->
                <div class="form-group mb-0">
                    <label class="font-weight-bold">
                        Descrizione del problema <span class="text-danger">*</span>
                    </label>
                    <textarea name="note" class="form-control" rows="5"
                              placeholder="Descriva il problema nel modo più dettagliato possibile..."
                              required maxlength="2000"><?= esc(old('note')) ?></textarea>
                    <small class="form-text text-muted">Max 2000 caratteri.</small>
                </div>

            </div>
            <div class="p-card-footer">
                <a href="<?= base_url('portale') ?>" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Annulla
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-paper-plane mr-1"></i> Invia Richiesta
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.tipo-card').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        document.getElementById('tipo_intervento').value = this.dataset.value;
        document.getElementById('submitBtn').disabled = false;
    });
});
if (document.getElementById('tipo_intervento').value) {
    document.getElementById('submitBtn').disabled = false;
}
</script>
<?= $this->endSection() ?>
