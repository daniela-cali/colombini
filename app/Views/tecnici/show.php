<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tecnici') ?>">Tecnici</a></li>
    <li class="breadcrumb-item active"><?= esc($tecnico->cognome . ' ' . $tecnico->nome) ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">

    <!-- Scheda anagrafica -->
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-body text-center pt-4">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x" style="color: var(--clr-teal);"></i>
                </div>
                <h4 class="mb-0"><?= esc($tecnico->nome . ' ' . $tecnico->cognome) ?></h4>
                <p class="text-muted mb-3">@<?= esc($tecnico->username) ?></p>
                <?php if ($tecnico->telefono): ?>
                    <p><i class="fas fa-phone mr-1 text-muted"></i>
                        <a href="tel:<?= esc($tecnico->telefono) ?>"><?= esc($tecnico->telefono) ?></a>
                    </p>
                <?php endif; ?>
                <span class="badge badge-info px-3 py-1">Tecnico</span>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="<?= base_url('tecnici') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Elenco
                </a>
                <div>
                    <a href="<?= base_url('tecnici/' . $tecnico->id . '/edit') ?>"
                       class="btn btn-sm btn-outline-primary mr-1">
                        <i class="fas fa-edit mr-1"></i> Modifica
                    </a>
                    <a href="<?= base_url('interventi/new?tecnico_id=' . $tecnico->id) ?>"
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Nuovo intervento
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Orari di lavoro -->
    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Orari di lavoro</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <form method="post" action="<?= base_url('tecnici/' . $tecnico->id . '/orari') ?>">
                <?= csrf_field() ?>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width:32px"></th>
                                <th>Giorno</th>
                                <th>Inizio</th>
                                <th>Fine</th>
                                <th class="text-muted">Pausa dalle</th>
                                <th class="text-muted">Pausa alle</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($giorni as $g => $label):
                            $o           = $orari[$g] ?? null;
                            $attivo      = $o ? (bool) $o['attivo'] : ($g <= 5);
                            $oraInizio   = $o['ora_inizio']   ?? $orariDefault['ora_inizio'];
                            $oraFine     = $o['ora_fine']     ?? $orariDefault['ora_fine'];
                            $pausaInizio = $o['pausa_inizio'] ?? $orariDefault['pausa_inizio'];
                            $pausaFine   = $o['pausa_fine']   ?? $orariDefault['pausa_fine'];
                            $pausaOn     = !empty($o['pausa_inizio']);
                        ?>
                            <tr>
                                <td class="text-center align-middle">
                                    <input type="checkbox" name="attivo[<?= $g ?>]" value="1"
                                           id="attivo_<?= $g ?>" <?= $attivo ? 'checked' : '' ?>
                                           onchange="toggleRow(<?= $g ?>)">
                                </td>
                                <td class="align-middle">
                                    <label for="attivo_<?= $g ?>" class="mb-0 <?= !$attivo ? 'text-muted' : '' ?>" id="label_<?= $g ?>">
                                        <?= $label ?>
                                    </label>
                                </td>
                                <td>
                                    <input type="time" name="ora_inizio[<?= $g ?>]"
                                           class="form-control form-control-sm" style="width:105px"
                                           value="<?= esc(substr($oraInizio, 0, 5)) ?>"
                                           <?= !$attivo ? 'disabled' : '' ?>
                                           id="inizio_<?= $g ?>">
                                </td>
                                <td>
                                    <input type="time" name="ora_fine[<?= $g ?>]"
                                           class="form-control form-control-sm" style="width:105px"
                                           value="<?= esc(substr($oraFine, 0, 5)) ?>"
                                           <?= !$attivo ? 'disabled' : '' ?>
                                           id="fine_<?= $g ?>">
                                </td>
                                <td>
                                    <input type="time" name="pausa_inizio[<?= $g ?>]"
                                           class="form-control form-control-sm" style="width:105px"
                                           value="<?= $pausaOn ? esc(substr($pausaInizio, 0, 5)) : '' ?>"
                                           placeholder="—"
                                           <?= !$attivo ? 'disabled' : '' ?>
                                           id="pausa_inizio_<?= $g ?>">
                                </td>
                                <td>
                                    <input type="time" name="pausa_fine[<?= $g ?>]"
                                           class="form-control form-control-sm" style="width:105px"
                                           value="<?= $pausaOn ? esc(substr($pausaFine, 0, 5)) : '' ?>"
                                           placeholder="—"
                                           <?= !$attivo ? 'disabled' : '' ?>
                                           id="pausa_fine_<?= $g ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save mr-1"></i> Salva orari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Interventi assegnati -->
    <div class="col-md-8">
        <?php
            $meseCorrente  = date('Y-m');
            $cPianificati  = 0; $cInCorso = 0; $cCompletatiMese = 0; $cAperti = 0;
            foreach ($interventi as $inv) {
                if ($inv['stato'] === 'pianificato') $cPianificati++;
                if ($inv['stato'] === 'in_corso')    $cInCorso++;
                if ($inv['stato'] === 'completato' &&
                    substr($inv['data_completamento'] ?? '', 0, 7) === $meseCorrente) $cCompletatiMese++;
                if (in_array($inv['stato'], ['pianificato','in_corso'])) $cAperti++;
            }
        ?>
        <div class="row mb-3">
            <div class="col-4">
                <div class="info-box mb-0 shadow-none border">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text small">Pianificati</span>
                        <span class="info-box-number"><?= $cPianificati ?></span>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box mb-0 shadow-none border">
                    <span class="info-box-icon bg-warning"><i class="fas fa-spinner"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text small">In corso</span>
                        <span class="info-box-number"><?= $cInCorso ?></span>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box mb-0 shadow-none border">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text small">Complet. mese</span>
                        <span class="info-box-number"><?= $cCompletatiMese ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Interventi assegnati</h3>
                <div class="card-tools">
                    <span class="badge badge-primary"><?= count($interventi) ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($interventi)): ?>
                    <div class="text-center py-4 text-muted">
                        <p>Nessun intervento assegnato.</p>
                        <a href="<?= base_url('interventi/new?tecnico_id=' . $tecnico->id) ?>"
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-plus mr-1"></i> Assegna intervento
                        </a>
                    </div>
                <?php else: ?>
                    <table class="table table-hover mb-0 table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Stato</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($interventi as $inv): ?>
                            <tr>
                                <td class="text-muted"><?= $inv['id'] ?></td>
                                <td><?= esc($tipi[$inv['tipo_intervento']] ?? $inv['tipo_intervento']) ?></td>
                                <td><?= $inv['cliente_nome'] ? esc($inv['cliente_cognome'] . ' ' . $inv['cliente_nome']) : '—' ?></td>
                                <td class="small text-muted">
                                    <?= $inv['data_pianificata'] ? date('d/m/Y', strtotime($inv['data_pianificata'])) : '—' ?>
                                </td>
                                <td>
                                    <?php $s = $stati[$inv['stato']] ?? ['label' => $inv['stato'], 'badge' => 'badge-secondary']; ?>
                                    <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('interventi/' . $inv['id']) ?>"
                                       class="btn btn-xs btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleRow(g) {
    var checked = document.getElementById('attivo_' + g).checked;
    ['inizio_', 'fine_', 'pausa_inizio_', 'pausa_fine_'].forEach(function(p) {
        document.getElementById(p + g).disabled = !checked;
    });
    document.getElementById('label_' + g).classList.toggle('text-muted', !checked);
}
</script>
<?= $this->endSection() ?>
