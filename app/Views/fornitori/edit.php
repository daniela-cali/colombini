<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('fornitori') ?>">Fornitori</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('fornitori/' . $fornitore['id']) ?>">
        <?= esc($fornitore['ragione_sociale'] ?: trim(($fornitore['cognome'] ?? '') . ' ' . ($fornitore['nome'] ?? ''))) ?>
    </a></li>
    <li class="breadcrumb-item active">Modifica</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">

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

        <?php
            $isTipoPrivato = empty($fornitore['ragione_sociale']) && ($fornitore['cognome'] || $fornitore['nome']);
            $tipoSel       = old('tipo', $isTipoPrivato ? 'privato' : 'azienda');
        ?>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Modifica Fornitore</h3>
            </div>
            <form method="post" action="<?= base_url('fornitori/' . $fornitore['id']) ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-group">
                        <label>Tipo <span class="text-danger">*</span></label>
                        <div class="d-flex">
                            <div class="mr-3">
                                <input type="radio" name="tipo" id="tipo_azienda" value="azienda"
                                       class="d-none tipo-radio"
                                       <?= ($tipoSel === 'azienda') ? 'checked' : '' ?>>
                                <label for="tipo_azienda"
                                       class="btn btn-outline-primary px-4 tipo-label <?= ($tipoSel === 'azienda') ? 'active' : '' ?>">
                                    <i class="fas fa-building mr-1"></i> Azienda
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="tipo" id="tipo_pf" value="privato"
                                       class="d-none tipo-radio"
                                       <?= ($tipoSel === 'privato') ? 'checked' : '' ?>>
                                <label for="tipo_pf"
                                       class="btn btn-outline-primary px-4 tipo-label <?= ($tipoSel === 'privato') ? 'active' : '' ?>">
                                    <i class="fas fa-user mr-1"></i> Privato / Libero professionista
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group" id="field-ragsoc">
                        <label for="ragione_sociale">Ragione sociale</label>
                        <input type="text" name="ragione_sociale" id="ragione_sociale" class="form-control"
                               maxlength="150"
                               value="<?= esc(old('ragione_sociale', $fornitore['ragione_sociale'])) ?>">
                    </div>

                    <div class="form-row" id="field-persona">
                        <div class="form-group col-md-6">
                            <label for="cognome">Cognome</label>
                            <input type="text" name="cognome" id="cognome" class="form-control"
                                   maxlength="100"
                                   value="<?= esc(old('cognome', $fornitore['cognome'])) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nome">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control"
                                   maxlength="100"
                                   value="<?= esc(old('nome', $fornitore['nome'])) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="referente">Referente</label>
                            <input type="text" name="referente" id="referente" class="form-control"
                                   maxlength="100"
                                   value="<?= esc(old('referente', $fornitore['referente'])) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="piva">P.IVA</label>
                            <input type="text" name="piva" id="piva" class="form-control"
                                   maxlength="20"
                                   value="<?= esc(old('piva', $fornitore['piva'])) ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="indirizzo">Indirizzo</label>
                        <input type="text" name="indirizzo" id="indirizzo" class="form-control"
                               maxlength="255"
                               value="<?= esc(old('indirizzo', $fornitore['indirizzo'])) ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="citta">Città</label>
                            <input type="text" name="citta" id="citta" class="form-control"
                                   maxlength="100"
                                   value="<?= esc(old('citta', $fornitore['citta'])) ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="cap">CAP</label>
                            <input type="text" name="cap" id="cap" class="form-control"
                                   maxlength="10"
                                   value="<?= esc(old('cap', $fornitore['cap'])) ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="provincia">Provincia</label>
                            <input type="text" name="provincia" id="provincia" class="form-control"
                                   maxlength="5"
                                   value="<?= esc(old('provincia', $fornitore['provincia'])) ?>"
                                   placeholder="es. MI">
                        </div>
                    </div>

                    <hr>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="telefono">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control"
                                   maxlength="50"
                                   value="<?= esc(old('telefono', $fornitore['telefono'])) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   maxlength="255"
                                   value="<?= esc(old('email', $fornitore['email'])) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea name="note" id="note" class="form-control" rows="3"
                                  maxlength="3000"><?= esc(old('note', $fornitore['note'])) ?></textarea>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('fornitori/' . $fornitore['id']) ?>" class="btn btn-secondary float-left">
                        <i class="fas fa-times mr-1"></i> Annulla
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
<?= $this->include('help/fornitori/edit') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    function aggiornaTipo() {
        var val = document.querySelector('.tipo-radio:checked')?.value ?? 'azienda';
        document.querySelectorAll('.tipo-label').forEach(function (l) { l.classList.remove('active'); });
        var checked = document.querySelector('.tipo-radio:checked');
        if (checked) checked.nextElementSibling.classList.add('active');

        if (val === 'privato') {
            document.getElementById('field-ragsoc').style.display  = 'none';
            document.getElementById('field-persona').style.display = '';
        } else {
            document.getElementById('field-ragsoc').style.display  = '';
            document.getElementById('field-persona').style.display = 'none';
        }
    }
    document.querySelectorAll('.tipo-radio').forEach(function (r) {
        r.addEventListener('change', aggiornaTipo);
    });
    aggiornaTipo();
})();
</script>
<?= $this->endSection() ?>
