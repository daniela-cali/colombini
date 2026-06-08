<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('fornitori') ?>">Fornitori</a></li>
    <li class="breadcrumb-item active"><?= esc($nome_display) ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">

    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header py-2">
                <h3 class="card-title">Scheda fornitore</h3>
            </div>
            <div class="card-body text-center pt-4">
                <div class="mb-3">
                    <?php if ($fornitore['ragione_sociale']): ?>
                        <i class="fas fa-building fa-5x" style="color: var(--clr-teal);"></i>
                    <?php else: ?>
                        <i class="fas fa-user-circle fa-5x" style="color: var(--clr-teal);"></i>
                    <?php endif; ?>
                </div>
                <h4 class="mb-1"><?= esc($nome_display) ?></h4>
                <?php if ($fornitore['referente']): ?>
                    <p class="text-muted small mb-2">Ref. <?= esc($fornitore['referente']) ?></p>
                <?php endif; ?>
                <?php if ($fornitore['piva']): ?>
                    <span class="badge badge-light px-3 py-1">P.IVA <?= esc($fornitore['piva']) ?></span>
                <?php endif; ?>

                <?php if ($fornitore['telefono']): ?>
                    <p class="mt-3 mb-1">
                        <i class="fas fa-phone mr-1 text-muted"></i>
                        <a href="tel:<?= esc($fornitore['telefono']) ?>"><?= esc($fornitore['telefono']) ?></a>
                    </p>
                <?php endif; ?>
                <?php if ($fornitore['email']): ?>
                    <p class="mb-1">
                        <i class="fas fa-envelope mr-1 text-muted"></i>
                        <a href="mailto:<?= esc($fornitore['email']) ?>"><?= esc($fornitore['email']) ?></a>
                    </p>
                <?php endif; ?>
                <?php if ($fornitore['indirizzo'] || $fornitore['citta']): ?>
                    <p class="mb-1 text-muted small">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <?php
                            $addr = array_filter([
                                $fornitore['indirizzo'],
                                $fornitore['cap'],
                                $fornitore['citta'],
                                $fornitore['provincia'] ? '(' . $fornitore['provincia'] . ')' : null,
                            ]);
                            echo esc(implode(', ', $addr));
                        ?>
                    </p>
                <?php endif; ?>
                <?php if ($fornitore['note']): ?>
                    <hr>
                    <p class="text-muted small text-left"><?= nl2br(esc($fornitore['note'])) ?></p>
                <?php endif; ?>
            </div>
            <div class="card-footer clearfix">
                <a href="<?= base_url('fornitori') ?>" class="btn btn-sm btn-secondary float-left">
                    <i class="fas fa-arrow-left mr-1"></i> Elenco
                </a>
                <div class="float-right">
                    <a href="<?= base_url('fornitori/' . $fornitore['id'] . '/edit') ?>" class="btn btn-sm btn-primary mr-1">
                        <i class="fas fa-edit mr-1"></i> Modifica
                    </a>
                    <form method="post" action="<?= base_url('fornitori/' . $fornitore['id'] . '/elimina') ?>"
                          class="d-inline"
                          onsubmit="return confirm('Eliminare questo fornitore?')">
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
                    <i class="fas fa-boxes mr-1"></i> Articoli forniti
                </h3>
                <div class="card-tools">
                    <span class="badge badge-secondary"><?= count($articoli) ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($articoli)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-box-open fa-2x mb-2"></i>
                        <p class="mb-0">Nessun articolo collegato a questo fornitore.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Codice</th>
                                    <th>Descrizione</th>
                                    <th>Giacenza</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($articoli as $a): ?>
                                <tr>
                                    <td class="align-middle small text-muted"><?= esc($a['cod_articolo']) ?></td>
                                    <td class="align-middle">
                                        <a href="<?= base_url('magazzino/' . $a['id']) ?>"><?= esc($a['descrizione']) ?></a>
                                    </td>
                                    <td class="align-middle">
                                        <?php $sottoScorta = $a['scorta_min'] > 0 && $a['giacenza_corrente'] < $a['scorta_min']; ?>
                                        <span class="badge <?= $sottoScorta ? 'badge-warning' : 'badge-light' ?>">
                                            <?= $a['giacenza_corrente'] ?> <?= esc($a['unita_misura']) ?>
                                        </span>
                                        <?php if ($sottoScorta): ?>
                                            <small class="text-warning ml-1" title="Sotto scorta minima">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </small>
                                        <?php endif; ?>
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
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/fornitori/show') ?>
<?= $this->endSection() ?>
