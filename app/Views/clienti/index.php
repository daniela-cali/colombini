<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Clienti</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<style>
#table-clienti_wrapper .dataTables_info { padding-top: 0; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title mb-0">Elenco clienti</h3>
        <div class="card-tools">
            <a href="<?= base_url('clienti/import') ?>" class="btn btn-outline-secondary btn-sm mr-1">
                <i class="fas fa-file-csv mr-1"></i>
                <span class="d-none d-sm-inline">Import CSV</span>
                <span class="d-sm-none">Import</span>
            </a>
            <a href="<?= base_url('clienti/new') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>
                <span class="d-none d-sm-inline">Nuovo cliente</span>
                <span class="d-sm-none">Nuovo</span>
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($clienti)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users fa-3x mb-3"></i>
                <p>Nessun cliente ancora inserito.</p>
                <a href="<?= base_url('clienti/new') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Aggiungi cliente
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="table-clienti" class="table table-hover mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Nominativo</th>
                            <th>Tipo</th>
                            <th>Città</th>
                            <th>Telefono</th>
                            <th>Portale</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($clienti as $c): ?>
                        <tr>
                            <td class="small text-muted align-middle"><?= esc($c['codice']) ?></td>
                            <td class="align-middle">
                                <a href="<?= base_url('clienti/' . $c['id']) ?>">
                                    <?php if ($c['tipo'] === 'persona_fisica'): ?>
                                        <?= esc(trim(($c['cognome'] ?? '') . ' ' . ($c['nome'] ?? ''))) ?>
                                    <?php else: ?>
                                        <?= esc($c['ragsoc'] ?? '') ?>
                                    <?php endif; ?>
                                </a>
                                <?php if (empty($c['geocoded_at'])): ?>
                                    <?php if ($c['geocodifica_fallita'] ?? 0): ?>
                                        <span class="badge badge-warning ml-1" title="Geocodifica fallita — indirizzo non trovato">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-danger ml-1" title="Non geocodificato — escluso dall'ottimizzazione">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <?= $c['tipo'] === 'persona_fisica'
                                    ? '<span class="badge badge-light">Persona fisica</span>'
                                    : '<span class="badge badge-light">Società</span>' ?>
                            </td>
                            <td class="align-middle">
                                <?= esc($c['citta'] ?? '') ?>
                                <?php if ($c['provincia'] ?? ''): ?>
                                    <span class="text-muted">(<?= esc($c['provincia']) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <?= $c['telefono'] ? esc($c['telefono']) : '' ?>
                            </td>
                            <td class="align-middle">
                                <?php if ($c['user_id']): ?>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Attivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right align-middle">
                                <a href="<?= base_url('clienti/' . $c['id']) ?>"
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
<?= $this->include('help/clienti/index') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script>
$(function () {
    $('#table-clienti').DataTable({
        language: {
            search:           'Cerca:',
            lengthMenu:       'Mostra _MENU_',
            info:             'Da _START_ a _END_ di _TOTAL_ clienti',
            infoEmpty:        'Nessun cliente',
            infoFiltered:     '(filtrati da _MAX_ totali)',
            zeroRecords:      'Nessun cliente trovato',
            emptyTable:       'Nessun cliente disponibile',
            paginate: {
                first:    'Prima',
                last:     'Ultima',
                next:     'Succ.',
                previous: 'Prec.'
            }
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
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });
});
</script>
<?= $this->endSection() ?>
