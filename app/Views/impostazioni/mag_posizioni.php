<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
    <li class="breadcrumb-item active">Posizioni scaffale</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">

    <!-- Lista posizioni -->
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Posizioni scaffale</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($posizioni)): ?>
                    <div class="text-center py-4 text-muted">Nessuna posizione definita.</div>
                <?php else: ?>
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nome</th>
                            <th class="text-center" style="width:80px">Attiva</th>
                            <th style="width:120px"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($posizioni as $pos): ?>
                        <tr>
                            <td class="align-middle"><?= esc($pos['nome']) ?></td>
                            <td class="align-middle text-center">
                                <?php if ($pos['attiva']): ?>
                                    <span class="badge badge-success">Sì</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle text-right">
                                <a href="<?= base_url('impostazioni/mag-posizioni/' . $pos['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-outline-primary mr-1" title="Modifica">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="post"
                                      action="<?= base_url('impostazioni/mag-posizioni/' . $pos['id'] . '/elimina') ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare la posizione «<?= esc($pos['nome']) ?>»?')">
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
                <h3 class="card-title"><?= $isEdit ? 'Modifica posizione' : 'Nuova posizione' ?></h3>
            </div>
            <form method="post" action="<?= $isEdit
                ? base_url('impostazioni/mag-posizioni/' . $editing['id'])
                : base_url('impostazioni/mag-posizioni') ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <div class="form-group">
                        <label for="nome">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" id="nome" class="form-control"
                               maxlength="50" required
                               value="<?= esc(old('nome', $editing['nome'] ?? '')) ?>">
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="attiva" value="0">
                            <input type="checkbox" name="attiva" id="attiva" value="1"
                                   class="custom-control-input"
                                   <?= old('attiva', $editing['attiva'] ?? 1) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="attiva">Posizione attiva</label>
                        </div>
                    </div>

                </div>
                <div class="card-footer clearfix">
                    <?php if ($isEdit): ?>
                        <a href="<?= base_url('impostazioni/mag-posizioni') ?>"
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
<?= $this->include('help/impostazioni/mag_posizioni') ?>
<?= $this->endSection() ?>
