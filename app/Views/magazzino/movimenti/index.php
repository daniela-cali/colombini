<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('magazzino') ?>">Magazzino</a></li>
    <li class="breadcrumb-item active">Movimenti</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title mb-0">Movimenti magazzino</h3>
        <div class="card-tools">
            <a href="<?= base_url('magazzino/movimenti/new') ?>" class="btn btn-sm">
                <i class="fas fa-plus mr-1"></i>
                <span class="d-none d-sm-inline">Nuovo movimento</span>
                <span class="d-sm-none">Nuovo</span>
            </a>
        </div>
    </div>

    <!-- Filtri -->
    <div class="card-body border-bottom py-2">
        <form method="get" action="<?= base_url('magazzino/movimenti') ?>" class="form-inline flex-wrap" style="gap:.5rem">
            <select name="tipo" class="form-control form-control-sm">
                <option value="">Tutti i tipi</option>
                <?php foreach ($tipi as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $tipo_sel === $k ? 'selected' : '' ?>><?= esc($v) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="dal" class="form-control form-control-sm" value="<?= esc($dal ?? '') ?>" placeholder="Dal">
            <input type="date" name="al"  class="form-control form-control-sm" value="<?= esc($al  ?? '') ?>" placeholder="Al">
            <button type="submit" class="btn btn-sm btn-secondary">
                <i class="fas fa-filter mr-1"></i> Filtra
            </button>
            <?php if ($tipo_sel || $dal || $al): ?>
                <a href="<?= base_url('magazzino/movimenti') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body p-0">
        <?php if (empty($movimenti)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                <p>Nessun movimento<?= ($tipo_sel || $dal || $al) ? ' con i filtri selezionati' : '' ?>.</p>
                <?php if (! $tipo_sel && ! $dal && ! $al): ?>
                    <a href="<?= base_url('magazzino/movimenti/new') ?>" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Registra primo movimento
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="table-movimenti" class="table table-hover mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Fornitore / Controparte</th>
                            <th>Righe</th>
                            <th>Registrato da</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($movimenti as $m): ?>
                        <?php
                            $isCarico    = in_array($m['tipo'], ['carico', 'rettifica_p', 'inventario']);
                            $badgeClass  = $isCarico ? 'badge-success' : 'badge-warning';
                            $controparte = $m['fornitore_nome'] ?? $m['cliente_nome'] ?? '';
                        ?>
                        <tr>
                            <td class="align-middle"><?= date('d/m/Y', strtotime($m['data'])) ?></td>
                            <td class="align-middle">
                                <span class="badge <?= $badgeClass ?>">
                                    <?= esc($tipi[$m['tipo']] ?? $m['tipo']) ?>
                                </span>
                            </td>
                            <td class="align-middle small text-muted"><?= esc($m['num_documento'] ?? '') ?></td>
                            <td class="align-middle small"><?= esc($controparte) ?></td>
                            <td class="align-middle text-center">
                                <span class="badge badge-light"><?= count($m['righe'] ?? []) ?></span>
                            </td>
                            <td class="align-middle small text-muted"><?= esc($m['utente_nome'] ?? '') ?></td>
                            <td class="text-right align-middle">
                                <a href="<?= base_url('magazzino/movimenti/' . $m['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="Dettaglio">
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
<?= $this->include('help/magazzino/movimenti_index') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script>
$(function () {
    $('#table-movimenti').DataTable({
        language: {
            search:       'Cerca:',
            lengthMenu:   'Mostra _MENU_',
            info:         'Da _START_ a _END_ di _TOTAL_ movimenti',
            infoEmpty:    'Nessun movimento',
            infoFiltered: '(filtrati da _MAX_ totali)',
            zeroRecords:  'Nessun movimento trovato',
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
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: -1 }]
    });
});
</script>
<?= $this->endSection() ?>
