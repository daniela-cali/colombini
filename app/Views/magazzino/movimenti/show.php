<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('magazzino') ?>">Magazzino</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('magazzino/movimenti') ?>">Movimenti</a></li>
    <li class="breadcrumb-item active">#<?= $movimento['id'] ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
    $isCarico   = in_array($movimento['tipo'], ['carico', 'rettifica_p', 'inventario']);
    $badgeClass = $isCarico ? 'badge-success' : 'badge-warning';
    $etichetta  = $tipi[$movimento['tipo']] ?? $movimento['tipo'];
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header py-2">
                <h3 class="card-title">Dettaglio Movimento</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <span class="badge <?= $badgeClass ?> px-3 py-2" style="font-size:1em">
                        <?= esc($etichetta) ?>
                    </span>
                </div>
                <table class="table table-sm table-borderless small mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Data</td>
                        <td><strong><?= date('d/m/Y', strtotime($movimento['data'])) ?></strong></td>
                    </tr>
                    <?php if ($movimento['num_documento']): ?>
                    <tr>
                        <td class="text-muted">Documento</td>
                        <td><?= esc($movimento['num_documento']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($movimento['fornitore_nome']): ?>
                    <tr>
                        <td class="text-muted">Fornitore</td>
                        <td>
                            <a href="<?= base_url('fornitori/' . $movimento['fornitore_id']) ?>">
                                <?= esc($movimento['fornitore_nome']) ?>
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($movimento['cliente_nome']): ?>
                    <tr>
                        <td class="text-muted">Cliente</td>
                        <td><?= esc($movimento['cliente_nome']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="text-muted">Registrato da</td>
                        <td><?= esc($movimento['utente_nome'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Righe</td>
                        <td><?= count($movimento['righe']) ?></td>
                    </tr>
                    <?php if ($movimento['note']): ?>
                    <tr>
                        <td class="text-muted">Note</td>
                        <td><?= nl2br(esc($movimento['note'])) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            <div class="card-footer clearfix">
                <a href="<?= base_url('magazzino/movimenti') ?>" class="btn btn-sm btn-secondary float-left">
                    <i class="fas fa-arrow-left mr-1"></i> Elenco
                </a>
                <form method="post"
                      action="<?= base_url('magazzino/movimenti/' . $movimento['id'] . '/elimina') ?>"
                      class="float-right"
                      onsubmit="return confirm('Annullare questo movimento? Le giacenze verranno ripristinate.')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-undo mr-1"></i> Annulla movimento
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i> Articoli
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Descrizione</th>
                                <th class="text-right">Quantità</th>
                                <?php if ($isCarico): ?>
                                <th class="text-right">Costo unit.</th>
                                <th class="text-right">Totale riga</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $totale = 0;
                            foreach ($movimento['righe'] as $r):
                                $rigaTot = ($r['costo_unitario'] ?? 0) * $r['quantita'];
                                $totale += $rigaTot;
                        ?>
                            <tr>
                                <td class="align-middle small font-weight-bold">
                                    <a href="<?= base_url('magazzino/' . $r['articolo_id']) ?>">
                                        <?= esc($r['cod_articolo'] ?? '') ?>
                                    </a>
                                </td>
                                <td class="align-middle"><?= esc($r['descrizione'] ?? '') ?></td>
                                <td class="align-middle text-right">
                                    <strong><?= $isCarico ? '+' : '−' ?><?= $r['quantita'] ?></strong>
                                    <small class="text-muted"><?= esc($r['unita_misura'] ?? '') ?></small>
                                </td>
                                <?php if ($isCarico): ?>
                                <td class="align-middle text-right small">
                                    <?= $r['costo_unitario'] > 0 ? '€ ' . number_format($r['costo_unitario'], 2, ',', '.') : '—' ?>
                                </td>
                                <td class="align-middle text-right small">
                                    <?= $rigaTot > 0 ? '€ ' . number_format($rigaTot, 2, ',', '.') : '—' ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <?php if ($isCarico && $totale > 0): ?>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-right font-weight-bold">Totale DDT</td>
                                <td class="text-right font-weight-bold">€ <?= number_format($totale, 2, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/magazzino/movimenti_show') ?>
<?= $this->endSection() ?>
