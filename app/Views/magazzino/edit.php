<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('magazzino') ?>">Magazzino</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('magazzino/' . $articolo['id']) ?>"><?= esc($articolo['cod_articolo']) ?></a></li>
    <li class="breadcrumb-item active">Modifica</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">

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

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Modifica Articolo</h3>
            </div>
            <form method="post" action="<?= base_url('magazzino/' . $articolo['id']) ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="cod_articolo">Codice articolo <span class="text-danger">*</span></label>
                            <input type="text" name="cod_articolo" id="cod_articolo" class="form-control"
                                   maxlength="20" style="text-transform:uppercase"
                                   value="<?= esc(old('cod_articolo', $articolo['cod_articolo'])) ?>">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="categoria_id">Categoria</label>
                            <select name="categoria_id" id="categoria_id" class="form-control">
                                <option value="">— nessuna —</option>
                                <?php foreach ($categorie as $cid => $cnome): ?>
                                    <option value="<?= $cid ?>"
                                        <?= old('categoria_id', $articolo['categoria_id']) == $cid ? 'selected' : '' ?>>
                                        <?= esc($cnome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="unita_misura">Unità di misura</label>
                            <input type="text" name="unita_misura" id="unita_misura" class="form-control"
                                   maxlength="10"
                                   value="<?= esc(old('unita_misura', $articolo['unita_misura'])) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descrizione">Descrizione <span class="text-danger">*</span></label>
                        <input type="text" name="descrizione" id="descrizione" class="form-control"
                               maxlength="500"
                               value="<?= esc(old('descrizione', $articolo['descrizione'])) ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fornitore_id">Fornitore</label>
                            <select name="fornitore_id" id="fornitore_id" class="form-control">
                                <option value="">— nessuno —</option>
                                <?php foreach ($fornitori as $fid => $fnome): ?>
                                    <option value="<?= $fid ?>"
                                        <?= old('fornitore_id', $articolo['fornitore_id']) == $fid ? 'selected' : '' ?>>
                                        <?= esc($fnome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="posizione_id">Posizione scaffale</label>
                            <select name="posizione_id" id="posizione_id" class="form-control">
                                <option value="">— nessuna —</option>
                                <?php foreach ($posizioni as $pid => $pnome): ?>
                                    <option value="<?= $pid ?>"
                                        <?= old('posizione_id', $articolo['posizione_id']) == $pid ? 'selected' : '' ?>>
                                        <?= esc($pnome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="coordinate">Coordinate</label>
                            <input type="text" name="coordinate" id="coordinate" class="form-control"
                                   maxlength="20"
                                   value="<?= esc(old('coordinate', $articolo['coordinate'])) ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="scorta_min">Scorta minima</label>
                            <input type="number" name="scorta_min" id="scorta_min" class="form-control"
                                   min="0" step="1"
                                   value="<?= esc(old('scorta_min', $articolo['scorta_min'])) ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="lotto_riordino">Lotto di riordino</label>
                            <input type="number" name="lotto_riordino" id="lotto_riordino" class="form-control"
                                   min="0" step="1"
                                   value="<?= esc(old('lotto_riordino', $articolo['lotto_riordino'])) ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="prezzo_acquisto">Prezzo listino (€)</label>
                            <input type="number" name="prezzo_acquisto" id="prezzo_acquisto" class="form-control"
                                   min="0" step="0.01"
                                   value="<?= esc(old('prezzo_acquisto', $articolo['prezzo_acquisto'])) ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="prezzo_vendita">Prezzo vendita (€)</label>
                            <input type="number" name="prezzo_vendita" id="prezzo_vendita" class="form-control"
                                   min="0" step="0.01"
                                   value="<?= esc(old('prezzo_vendita', $articolo['prezzo_vendita'])) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="perc_sconto_1">Sconto 1 (%)</label>
                            <input type="number" name="perc_sconto_1" id="perc_sconto_1" class="form-control"
                                   min="0" max="100" step="0.01"
                                   value="<?= esc(old('perc_sconto_1', $articolo['perc_sconto_1'])) ?>"
                                   placeholder="es. 30">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="perc_sconto_2">Sconto 2 (%)</label>
                            <input type="number" name="perc_sconto_2" id="perc_sconto_2" class="form-control"
                                   min="0" max="100" step="0.01"
                                   value="<?= esc(old('perc_sconto_2', $articolo['perc_sconto_2'])) ?>"
                                   placeholder="es. 10">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="perc_sconto_3">Sconto 3 (%)</label>
                            <input type="number" name="perc_sconto_3" id="perc_sconto_3" class="form-control"
                                   min="0" max="100" step="0.01"
                                   value="<?= esc(old('perc_sconto_3', $articolo['perc_sconto_3'])) ?>"
                                   placeholder="es. 5">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Prezzo netto calcolato (€)</label>
                            <input type="text" id="prezzo_netto_calc" class="form-control bg-light"
                                   readonly value="0.00">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea name="note" id="note" class="form-control" rows="3"
                                  maxlength="3000"><?= esc(old('note', $articolo['note'])) ?></textarea>
                    </div>

                    <div class="alert alert-light border small">
                        <i class="fas fa-info-circle mr-1 text-muted"></i>
                        La giacenza non si modifica qui — viene aggiornata automaticamente dai movimenti di magazzino.
                        Attuale: <strong><?= $articolo['giacenza_corrente'] ?> <?= esc($articolo['unita_misura']) ?></strong>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('magazzino/' . $articolo['id']) ?>" class="btn btn-secondary float-left">
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

<?= $this->section('scripts') ?>
<script>
function aggiornaNetto() {
    var listino = parseFloat(document.getElementById('prezzo_acquisto').value) || 0;
    var s1 = parseFloat(document.getElementById('perc_sconto_1').value) || 0;
    var s2 = parseFloat(document.getElementById('perc_sconto_2').value) || 0;
    var s3 = parseFloat(document.getElementById('perc_sconto_3').value) || 0;
    var netto = listino * (1 - s1/100) * (1 - s2/100) * (1 - s3/100);
    document.getElementById('prezzo_netto_calc').value = netto.toFixed(2);
}
['prezzo_acquisto','perc_sconto_1','perc_sconto_2','perc_sconto_3'].forEach(function(id) {
    document.getElementById(id).addEventListener('input', aggiornaNetto);
});
aggiornaNetto();
</script>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/magazzino/edit') ?>
<?= $this->endSection() ?>
