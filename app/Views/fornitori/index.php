<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Fornitori</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>


<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title mb-0">Elenco fornitori</h3>
        <div class="card-tools">
            <a href="<?= base_url('fornitori/new') ?>" class="btn btn-sm">
                <i class="fas fa-plus mr-1"></i>
                <span class="d-none d-sm-inline">Nuovo fornitore</span>
                <span class="d-sm-none">Nuovo</span>
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($fornitori)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-truck fa-3x mb-3"></i>
                <p>Nessun fornitore ancora inserito.</p>
                <a href="<?= base_url('fornitori/new') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Aggiungi fornitore
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="table-fornitori" class="table table-hover mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nominativo</th>
                            <th>Referente</th>
                            <th>Telefono</th>
                            <th>Email</th>
                            <th>Città</th>
                            <th>P.IVA</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fornitori as $f): ?>
                        <?php
                            if ($f['ragione_sociale']) {
                                $nome = esc($f['ragione_sociale']);
                                $icon = 'fas fa-building text-muted';
                            } else {
                                $nome = esc(trim(($f['cognome'] ?? '') . ' ' . ($f['nome'] ?? '')));
                                $icon = 'fas fa-user text-muted';
                            }
                        ?>
                        <tr>
                            <td class="align-middle">
                                <i class="<?= $icon ?> mr-1 small"></i>
                                <a href="<?= base_url('fornitori/' . $f['id']) ?>"><?= $nome ?></a>
                            </td>
                            <td class="align-middle"><?= esc($f['referente'] ?? '') ?></td>
                            <td class="align-middle">
                                <?php if ($f['telefono']): ?>
                                    <a href="tel:<?= esc($f['telefono']) ?>"><?= esc($f['telefono']) ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <?php if ($f['email']): ?>
                                    <a href="mailto:<?= esc($f['email']) ?>"><?= esc($f['email']) ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <?= esc($f['citta'] ?? '') ?>
                                <?php if ($f['provincia'] ?? ''): ?>
                                    <span class="text-muted">(<?= esc($f['provincia']) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle small text-muted"><?= esc($f['piva'] ?? '') ?></td>
                            <td class="text-right align-middle">
                                <a href="<?= base_url('fornitori/' . $f['id']) ?>"
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
<?= $this->include('help/fornitori/index') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script>
$(function () {
    $('#table-fornitori').DataTable({
        language: {
            search:       'Cerca:',
            lengthMenu:   'Mostra _MENU_',
            info:         'Da _START_ a _END_ di _TOTAL_ fornitori',
            infoEmpty:    'Nessun fornitore',
            infoFiltered: '(filtrati da _MAX_ totali)',
            zeroRecords:  'Nessun fornitore trovato',
            emptyTable:   'Nessun fornitore disponibile',
            paginate: { first: 'Prima', last: 'Ultima', next: 'Succ.', previous: 'Prec.' }
        },
        dom:
            "<'d-flex justify-content-end px-3 pt-2 pb-1'f>" +
            "t" +
            "<'d-flex flex-wrap align-items-center justify-content-between py-2 border-top'" +
                "<'d-flex align-items-center ml-3'<'mr-3'l>i>" +
                "<'mr-3'p>" +
            ">",
        pageLength: 25,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'Tutti']],
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }]
    });
});
</script>
<?= $this->endSection() ?>
