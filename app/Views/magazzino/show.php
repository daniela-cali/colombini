<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('magazzino') ?>">Magazzino</a></li>
    <li class="breadcrumb-item active"><?= esc($articolo['cod_articolo']) ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">

    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header py-2">
                <h3 class="card-title">Scheda articolo</h3>
            </div>
            <div class="card-body">

                <div class="text-center mb-3">
                    <i class="fas fa-box fa-4x" style="color: var(--clr-teal);"></i>
                </div>

                <h5 class="text-center mb-1"><?= esc($articolo['descrizione']) ?></h5>
                <p class="text-center text-muted small mb-1">
                    <code><?= esc($articolo['cod_articolo']) ?></code>
                </p>
                <?php if ($articolo['posizione_nome'] ?? null): ?>
                <p class="text-center mb-3">
                    <span class="badge badge-secondary px-2 py-1">
                        <i class="fas fa-map-marker-alt mr-1"></i><?= esc($articolo['posizione_nome']) ?>
                        <?php if ($articolo['coordinate']): ?>
                            &nbsp;·&nbsp;<?= esc($articolo['coordinate']) ?>
                        <?php endif; ?>
                    </span>
                </p>
                <?php else: ?>
                <div class="mb-3"></div>
                <?php endif; ?>

                <?php $sottoScorta = $articolo['scorta_min'] > 0 && $articolo['giacenza_corrente'] < $articolo['scorta_min']; ?>
                <div class="text-center mb-3">
                    <span class="badge badge-<?= $sottoScorta ? 'warning' : 'success' ?> px-3 py-2" style="font-size:1.1em">
                        <?= $articolo['giacenza_corrente'] ?> <?= esc($articolo['unita_misura']) ?>
                    </span>
                    <?php if ($sottoScorta): ?>
                        <p class="text-warning small mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Sotto scorta minima (min: <?= $articolo['scorta_min'] ?>)
                        </p>
                    <?php endif; ?>
                </div>

                <table class="table table-sm table-borderless small mb-0">
                    <tr>
                        <td class="text-muted" style="width:45%">Categoria</td>
                        <td><?= esc($articolo['categoria_nome'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fornitore</td>
                        <td>
                            <?php if ($articolo['fornitore_id']): ?>
                                <a href="<?= base_url('fornitori/' . $articolo['fornitore_id']) ?>">
                                    <?= esc($articolo['fornitore_nome'] ?? $articolo['fornitore_id']) ?>
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-muted">Scorta min.</td>
                        <td><?= $articolo['scorta_min'] ?: '—' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lotto riordino</td>
                        <td><?= $articolo['lotto_riordino'] ?: '—' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Prezzo listino</td>
                        <td><?= $articolo['prezzo_acquisto'] > 0 ? '€ ' . number_format($articolo['prezzo_acquisto'], 2, ',', '.') : '—' ?></td>
                    </tr>
                    <?php
                        $s1 = (float) ($articolo['perc_sconto_1'] ?? 0);
                        $s2 = (float) ($articolo['perc_sconto_2'] ?? 0);
                        $s3 = (float) ($articolo['perc_sconto_3'] ?? 0);
                        $haSconti = $s1 > 0 || $s2 > 0 || $s3 > 0;
                    ?>
                    <?php if ($haSconti): ?>
                    <tr>
                        <td class="text-muted">Sconti fornitore</td>
                        <td class="small">
                            <?php
                                $parti = [];
                                if ($s1 > 0) $parti[] = number_format($s1, 2, ',', '') . '%';
                                if ($s2 > 0) $parti[] = number_format($s2, 2, ',', '') . '%';
                                if ($s3 > 0) $parti[] = number_format($s3, 2, ',', '') . '%';
                                echo implode(' + ', $parti);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Prezzo netto</td>
                        <td>
                            <?php
                                $netto = $articolo['prezzo_acquisto'] * (1 - $s1/100) * (1 - $s2/100) * (1 - $s3/100);
                                echo $netto > 0 ? '<strong>€ ' . number_format($netto, 2, ',', '.') . '</strong>' : '—';
                            ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="text-muted">Prezzo vendita</td>
                        <td><?= $articolo['prezzo_vendita'] > 0 ? '€ ' . number_format($articolo['prezzo_vendita'], 2, ',', '.') : '—' ?></td>
                    </tr>
                </table>

                <?php if ($articolo['note']): ?>
                    <hr>
                    <p class="small text-muted"><?= nl2br(esc($articolo['note'])) ?></p>
                <?php endif; ?>

            </div>
            <div class="card-footer clearfix">
                <a href="<?= base_url('magazzino') ?>" class="btn btn-sm btn-secondary float-left">
                    <i class="fas fa-arrow-left mr-1"></i> Elenco
                </a>
                <div class="float-right">
                    <a href="<?= base_url('magazzino/' . $articolo['id'] . '/edit') ?>" class="btn btn-sm btn-primary mr-1">
                        <i class="fas fa-edit mr-1"></i> Modifica
                    </a>
                    <form method="post" action="<?= base_url('magazzino/' . $articolo['id'] . '/elimina') ?>"
                          class="d-inline"
                          onsubmit="return confirm('Eliminare questo articolo?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-1"></i> Storico movimenti
                </h3>
                <div class="card-tools">
                    <span class="badge badge-secondary"><?= count($storico) ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($storico)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">Nessun movimento registrato per questo articolo.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Documento</th>
                                    <th>Controparte</th>
                                    <th class="text-right">Qtà</th>
                                    <th class="text-right">Costo unit.</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($storico as $m): ?>
                                <?php
                                    $isCar   = in_array($m['tipo'], ['carico', 'rettifica_p', 'inventario']);
                                    $classe  = $isCar ? 'text-success' : 'text-danger';
                                    $segno   = $isCar ? '+' : '−';
                                    $etichetta = $tipi[$m['tipo']] ?? $m['tipo'];
                                    $controparte = $m['fornitore_nome'] ?? $m['cliente_nome'] ?? '—';
                                ?>
                                <tr>
                                    <td class="align-middle small">
                                        <?= date('d/m/Y', strtotime($m['data'])) ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-light"><?= esc($etichetta) ?></span>
                                    </td>
                                    <td class="align-middle small text-muted"><?= esc($m['num_documento'] ?? '') ?></td>
                                    <td class="align-middle small"><?= esc($controparte) ?></td>
                                    <td class="align-middle text-right <?= $classe ?>">
                                        <strong><?= $segno . $m['quantita'] ?></strong>
                                    </td>
                                    <td class="align-middle text-right small text-muted">
                                        <?= $m['costo_unitario'] > 0 ? '€ ' . number_format($m['costo_unitario'], 2, ',', '.') : '' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/magazzino/show') ?>
<?= $this->endSection() ?>
