<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Magazzino</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>


<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title mb-0">Articoli</h3>
        <div class="card-tools">
            <a href="<?= base_url('magazzino/new') ?>" class="btn btn-sm">
                <i class="fas fa-plus mr-1"></i>
                <span class="d-none d-sm-inline">Nuovo articolo</span>
                <span class="d-sm-none">Nuovo</span>
            </a>
        </div>
    </div>
    <?php if (!empty($categorie)): ?>
    <div class="card-body border-bottom pb-2 pt-2">
        <div class="d-flex flex-wrap gap-1" style="gap:.4rem">
            <a href="<?= base_url('magazzino') ?>"
               class="btn btn-sm <?= $categoria_id === null ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                Tutti
            </a>
            <?php foreach ($categorie as $cid => $cnome): ?>
                <a href="<?= base_url('magazzino?categoria=' . $cid) ?>"
                   class="btn btn-sm <?= $categoria_id === $cid ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                    <?= esc($cnome) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="card-body p-0">
        <?php if (empty($articoli)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-boxes fa-3x mb-3"></i>
                <p>Nessun articolo<?= $categoria_id ? ' in questa categoria' : '' ?>.</p>
                <a href="<?= base_url('magazzino/new') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Aggiungi articolo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="table-articoli" class="table table-hover mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Descrizione</th>
                            <th>Posizione</th>
                            <th>Categoria</th>
                            <th class="text-right">Giacenza</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($articoli as $a): ?>
                        <?php $sottoScorta = $a['scorta_min'] > 0 && $a['giacenza_corrente'] < $a['scorta_min']; ?>
                        <tr <?= $sottoScorta ? 'class="table-warning"' : '' ?>>
                            <td class="align-middle small font-weight-bold"><?= esc($a['cod_articolo']) ?></td>
                            <td class="align-middle">
                                <a href="<?= base_url('magazzino/' . $a['id']) ?>"><?= esc($a['descrizione']) ?></a>
                                <?php if ($sottoScorta): ?>
                                    <span class="badge badge-warning ml-1" title="Sotto scorta minima (min: <?= $a['scorta_min'] ?>)">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle small"><?= esc($a['posizione_nome'] ?? '') ?><?php if ($a['coordinate'] ?? ''): ?> <small class="text-muted"><?= esc($a['coordinate']) ?></small><?php endif; ?></td>
                            <td class="align-middle small"><?= esc($a['categoria_nome'] ?? '') ?></td>
                            <td class="align-middle text-right">
                                <strong><?= $a['giacenza_corrente'] ?></strong>
                                <small class="text-muted"><?= esc($a['unita_misura']) ?></small>
                            </td>
                            <td class="text-right align-middle">
                                <a href="<?= base_url('magazzino/' . $a['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="Scheda">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/magazzino/index') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script>
$(function () {
    $('#table-articoli').DataTable({
        language: {
            search:       'Cerca:',
            lengthMenu:   'Mostra _MENU_',
            info:         'Da _START_ a _END_ di _TOTAL_ articoli',
            infoEmpty:    'Nessun articolo',
            infoFiltered: '(filtrati da _MAX_ totali)',
            zeroRecords:  'Nessun articolo trovato',
            emptyTable:   'Nessun articolo disponibile',
            paginate: { first: 'Prima', last: 'Ultima', next: 'Succ.', previous: 'Prec.' }
        },
        dom:
            "<'d-flex justify-content-end px-3 pt-2 pb-1'f>" +
            "t" +
            "<'d-flex flex-wrap align-items-center justify-content-between py-2 border-top'" +
                "<'d-flex align-items-center ml-3'<'mr-3'l>i>" +
                "<'mr-3'p>" +
            ">",
        pageLength: 50,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'Tutti']],
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }]
    });
});
</script>
<?= $this->endSection() ?>
