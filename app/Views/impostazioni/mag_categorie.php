<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item active">Categorie magazzino</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">

    <!-- Lista categorie -->
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Categorie magazzino</h3>
                <div class="card-tools">
                    <span id="sort-indicator" style="min-width:20px;display:inline-block;"></span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($categorie)): ?>
                    <div class="text-center py-4 text-muted">Nessuna categoria definita.</div>
                <?php else: ?>
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <?php if (count($categorie) > 1): ?>
                            <th style="width:36px"></th>
                            <?php endif; ?>
                            <th>Nome</th>
                            <th class="text-center" style="width:80px">Attiva</th>
                            <th style="width:120px"></th>
                        </tr>
                    </thead>
                    <tbody id="lista-categorie">
                    <?php foreach ($categorie as $cat): ?>
                        <tr data-id="<?= $cat['id'] ?>">
                            <?php if (count($categorie) > 1): ?>
                            <td class="sort-handle text-center text-muted align-middle">
                                <i class="fas fa-grip-vertical"></i>
                            </td>
                            <?php endif; ?>
                            <td class="align-middle"><?= esc($cat['nome']) ?></td>
                            <td class="align-middle text-center">
                                <?php if ($cat['attiva']): ?>
                                    <span class="badge badge-success">Sì</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle text-right">
                                <a href="<?= base_url('impostazioni/mag-categorie/' . $cat['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-outline-primary mr-1" title="Modifica">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="post"
                                      action="<?= base_url('impostazioni/mag-categorie/' . $cat['id'] . '/elimina') ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare la categoria «<?= esc($cat['nome']) ?>»? Gli articoli collegati perderanno la categoria.')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Form aggiungi / modifica -->
    <div class="col-md-4">
        <?php $isEdit = !empty($editing); ?>
        <div class="card card-<?= $isEdit ? 'warning' : 'success' ?>">
            <div class="card-header">
                <h3 class="card-title"><?= $isEdit ? 'Modifica categoria' : 'Nuova categoria' ?></h3>
            </div>
            <form method="post" action="<?= $isEdit
                ? base_url('impostazioni/mag-categorie/' . $editing['id'])
                : base_url('impostazioni/mag-categorie') ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-group">
                        <label for="nome">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" id="nome" class="form-control"
                               maxlength="100" required
                               value="<?= esc(old('nome', $editing['nome'] ?? '')) ?>">
                    </div>

                    <div class="form-group">
                        <label for="ordine">Ordine</label>
                        <input type="number" name="ordine" id="ordine" class="form-control"
                               min="0" step="1"
                               value="<?= esc(old('ordine', $editing['ordine'] ?? $prossimo_ordine)) ?>">
                        <small class="text-muted">Determina la sequenza nei filtri e nei menu.</small>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="attiva" value="0">
                            <input type="checkbox" name="attiva" id="attiva" value="1"
                                   class="custom-control-input"
                                   <?= old('attiva', $editing['attiva'] ?? 1) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="attiva">Categoria attiva</label>
                        </div>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <?php if ($isEdit): ?>
                        <a href="<?= base_url('impostazioni/mag-categorie') ?>"
                           class="btn btn-secondary float-left">Annulla</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-<?= $isEdit ? 'warning' : 'success' ?> float-right">
                        <i class="fas fa-save mr-1"></i> <?= $isEdit ? 'Salva modifiche' : 'Aggiungi' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/impostazioni/mag_categorie') ?>
<?= $this->endSection() ?>

<?php if (count($categorie ?? []) > 1): ?>
<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    var tbody    = document.getElementById('lista-categorie');
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';
    var saveUrl  = '<?= base_url('impostazioni/mag-categorie/riordina') ?>';
    var indicator = document.getElementById('sort-indicator');

    Sortable.create(tbody, {
        handle:    '.sort-handle',
        animation: 150,
        onEnd: function () {
            var ids = Array.from(tbody.querySelectorAll('tr[data-id]'))
                          .map(function (tr) { return tr.dataset.id; });

            indicator.innerHTML = '<i class="fas fa-spinner fa-spin text-muted"></i>';

            var data = new FormData();
            data.append(csrfName, csrfHash);
            ids.forEach(function (id) { data.append('ids[]', id); });

            fetch(saveUrl, { method: 'POST', body: data })
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    if (json.csrf) { csrfHash = json.csrf; }
                    indicator.innerHTML = '<i class="fas fa-check text-success"></i>';
                    setTimeout(function () { indicator.innerHTML = ''; }, 1200);
                })
                .catch(function () {
                    indicator.innerHTML = '<i class="fas fa-exclamation-triangle text-danger" title="Errore nel salvataggio"></i>';
                });
        }
    });
})();
</script>
<?= $this->endSection() ?>
<?php endif; ?>
