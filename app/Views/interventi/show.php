<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('interventi') ?>">Interventi</a></li>
    <li class="breadcrumb-item active">#<?= $intervento['id'] ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Intervento #<?= $intervento['id'] ?></h3>
                <?php $s = $stati[$intervento['stato']] ?? ['label' => $intervento['stato'], 'badge' => 'badge-secondary']; ?>
                <span class="badge <?= $s['badge'] ?> px-3 py-2"><?= $s['label'] ?></span>
            </div>
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Tipo intervento</div>
                    <div class="col-sm-8"><?= esc($tipi[$intervento['tipo_intervento']] ?? $intervento['tipo_intervento']) ?></div>
                </div>

                <?php if ($intervento['luogo_intervento']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Luogo</div>
                    <div class="col-sm-8"><?= esc($intervento['luogo_intervento']) ?></div>
                </div>
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Cliente</div>
                    <div class="col-sm-8">
                        <?= $intervento['cliente_nome']
                            ? esc($intervento['cliente_cognome'] . ' ' . $intervento['cliente_nome'])
                            : '<span class="text-muted">—</span>' ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Tecnico</div>
                    <div class="col-sm-8">
                        <?php if ($intervento['tecnico_nome']): ?>
                            <?= esc($intervento['tecnico_cognome'] . ' ' . $intervento['tecnico_nome']) ?>
                            <?php if ($intervento['tecnico_telefono']): ?>
                                <br><small class="text-muted">
                                    <i class="fas fa-phone mr-1"></i><?= esc($intervento['tecnico_telefono']) ?>
                                </small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">Non assegnato</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Data pianificata</div>
                    <div class="col-sm-8">
                        <?= $intervento['data_pianificata']
                            ? date('d/m/Y \a\l\l\e H:i', strtotime($intervento['data_pianificata']))
                            : '<span class="text-muted">—</span>' ?>
                    </div>
                </div>

                <?php if ($intervento['data_completamento']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Completato il</div>
                    <div class="col-sm-8">
                        <?= date('d/m/Y \a\l\l\e H:i', strtotime($intervento['data_completamento'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['descrizione']): ?>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Descrizione</div>
                    <div class="col-sm-8">
                        <p class="mb-0" style="white-space:pre-wrap"><?= esc($intervento['descrizione']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['note_interne']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small font-weight-bold">Note interne</div>
                    <div class="col-sm-8">
                        <p class="mb-0 text-muted" style="white-space:pre-wrap"><?= esc($intervento['note_interne']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($intervento['richiesta_id']): ?>
                <hr>
                <div class="row">
                    <div class="col-sm-4 text-muted small font-weight-bold">Richiesta portale</div>
                    <div class="col-sm-8">
                        <span class="badge badge-info">
                            <i class="fas fa-link mr-1"></i>Richiesta #<?= $intervento['richiesta_id'] ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <div class="card-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <a href="<?= base_url('interventi') ?>" class="btn btn-secondary btn-sm mb-1">
                        <i class="fas fa-arrow-left mr-1"></i> Elenco
                    </a>
                    <div>
                        <a href="<?= base_url('interventi/' . $intervento['id'] . '/edit') ?>"
                           class="btn btn-warning btn-sm mb-1">
                            <i class="fas fa-edit mr-1"></i> Modifica
                        </a>
                        <?php if ($intervento['stato'] !== 'completato' && $intervento['stato'] !== 'annullato'): ?>
                            <a href="<?= base_url('interventi/' . $intervento['id'] . '/chiudi') ?>"
                               class="btn btn-success btn-sm mb-1"
                               onclick="return confirm('Chiudere l\'intervento come completato?')">
                                <i class="fas fa-check mr-1"></i> Chiudi
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('interventi/' . $intervento['id'] . '/pdf') ?>"
                           class="btn btn-outline-secondary btn-sm mb-1">
                            <i class="fas fa-print mr-1"></i> Stampa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title small text-muted">Creato il</h3>
            </div>
            <div class="card-body">
                <p class="mb-0"><?= date('d/m/Y \a\l\l\e H:i', strtotime($intervento['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
