<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item active">La mia agenda</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Widgets contatori -->
<?php
    $tid = auth()->user()->id;
    $urlBase = base_url('interventi?tecnico_id=' . $tid . '&stato=');
?>
<div class="row">
    <div class="col-6 col-lg-3">
        <a href="<?= $urlBase . 'pianificato' ?>" class="info-box" style="text-decoration:none;color:inherit;">
            <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pianificati</span>
                <span class="info-box-number"><?= $stats['pianificati'] ?></span>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="<?= $urlBase . 'in_corso' ?>" class="info-box" style="text-decoration:none;color:inherit;">
            <span class="info-box-icon bg-warning"><i class="fas fa-spinner"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">In corso</span>
                <span class="info-box-number"><?= $stats['in_corso'] ?></span>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="<?= $urlBase . 'completato' ?>" class="info-box" style="text-decoration:none;color:inherit;">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Completati questo mese</span>
                <span class="info-box-number"><?= $stats['completati_mese'] ?></span>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="<?= $urlBase . 'completato' ?>" class="info-box" style="text-decoration:none;color:inherit;">
            <span class="info-box-icon bg-primary"><i class="fas fa-tools"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Totale completati</span>
                <span class="info-box-number"><?= $stats['totale_completati'] ?></span>
            </div>
        </a>
    </div>
</div>

<!-- Prossimi interventi -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-check mr-1"></i> Prossimi interventi
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success mr-1" data-toggle="modal" data-target="#modalNuovoIntervento">
                        <i class="fas fa-plus mr-1"></i> Nuovo intervento
                    </button>
                    <a href="<?= base_url('interventi?tecnico_id=' . auth()->user()->id) ?>"
                       class="btn btn-sm btn-outline-light">
                        <i class="fas fa-list mr-1"></i> Tutti i miei interventi
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($prossimiPerGiorno)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <p class="mb-0">Nessun intervento in programma.</p>
                    </div>
                <?php else:
                    $giorniIt = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
                ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th style="width:60px;">Ora</th>
                                    <th>Tipo</th>
                                    <th>Cliente</th>
                                    <th class="d-none d-md-table-cell">Luogo</th>
                                    <th>Stato</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($prossimiPerGiorno as $giorno => $invs):
                                if ($giorno === 'senza_data') {
                                    $intestazione = 'Da pianificare';
                                    $rowClass     = 'table-light';
                                } else {
                                    $ts           = strtotime($giorno);
                                    $dow          = $giorniIt[(int) date('w', $ts)];
                                    $intestazione = $dow . ' ' . date('d/m/Y', $ts);
                                    $isOggi       = $giorno === date('Y-m-d');
                                    $rowClass     = $isOggi ? 'table-primary' : 'table-secondary';
                                }
                            ?>
                                <tr class="<?= $rowClass ?>">
                                    <td colspan="6" class="py-1 px-3">
                                        <strong style="font-size:.8rem;"><?= $intestazione ?></strong>
                                        <span class="badge badge-dark ml-1" style="font-size:.65rem;"><?= count($invs) ?></span>
                                    </td>
                                </tr>
                            <?php foreach ($invs as $inv):
                                $s = $stati[$inv['stato']] ?? ['label' => $inv['stato'], 'badge' => 'badge-secondary'];
                                $cliente = $inv['cliente_ragsoc']
                                    ?: trim(($inv['cliente_cognome'] ?? '') . ' ' . ($inv['cliente_nome'] ?? ''));
                                $ora = $inv['data_pianificata'] ? date('H:i', strtotime($inv['data_pianificata'])) : '—';
                            ?>
                                <tr>
                                    <td class="align-middle text-nowrap text-muted small"><?= $ora !== '00:00' ? $ora : '—' ?></td>
                                    <td class="align-middle">
                                        <i class="fas <?= esc($icone[$inv['tipo_intervento']] ?? 'fa-tools') ?> mr-1 text-muted"></i><?= esc($tipi[$inv['tipo_intervento']] ?? $inv['tipo_intervento']) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= $cliente ? esc($cliente) : '<span class="text-muted">—</span>' ?>
                                    </td>
                                    <td class="d-none d-md-table-cell align-middle text-muted small">
                                        <?= esc($inv['luogo_intervento'] ?? '—') ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
                                    </td>
                                    <td class="align-middle text-right">
                                        <a href="<?= base_url('interventi/' . $inv['id']) ?>" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Placeholder interventi nelle vicinanze -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-secondary">
            <div class="card-body text-center py-4 text-muted">
                <i class="fas fa-map-marked-alt fa-2x mb-2"></i>
                <p class="mb-0">
                    <strong>Interventi nelle vicinanze</strong> — disponibile nella prossima versione.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuovo Intervento Rapido -->
<div class="modal fade" id="modalNuovoIntervento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white"><i class="fas fa-plus mr-1"></i> Nuovo intervento rapido</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="<?= base_url('interventi') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="tecnico_id" value="<?= auth()->user()->id ?>">
                <input type="hidden" name="stato" value="pianificato">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipo di intervento <span class="text-danger">*</span></label>
                        <select name="tipo_intervento" class="form-control" required>
                            <option value="">— Seleziona —</option>
                            <?php foreach ($tipi as $key => $label): ?>
                                <option value="<?= $key ?>"><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cliente</label>
                        <select name="cliente_id" class="form-control">
                            <option value="">— Nessuno —</option>
                            <?php foreach ($clienti as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= esc($c['tipo'] === 'persona_fisica'
                                        ? trim(($c['cognome'] ?? '') . ' ' . ($c['nome'] ?? ''))
                                        : ($c['ragsoc'] ?? '')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>Data e ora <small class="text-muted">(opzionale)</small></label>
                        <input type="datetime-local" name="data_pianificata" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-arrow-right mr-1"></i> Crea e vai alla scheda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/dashboard/tecnico') ?>
<?= $this->endSection() ?>
