<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('viaggi?data=' . $viaggio['data']) ?>">Viaggi</a></li>
    <li class="breadcrumb-item active"><?= esc($viaggio['cognome'] . ' ' . $viaggio['nome']) ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">

    <!-- Colonna sinistra: riepilogo viaggio -->
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-body pt-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-4x"
                       style="color: <?= esc($viaggio['colore'] ?? 'var(--clr-teal)') ?>;"></i>
                </div>
                <h4 class="mb-1"><?= esc($viaggio['cognome'] . ' ' . $viaggio['nome']) ?></h4>
                <p class="text-muted small mb-2"><?= data_ita($viaggio['data']) ?></p>
                <?php $s = $stati[$viaggio['stato']] ?? ['label' => $viaggio['stato'], 'badge' => 'badge-secondary']; ?>
                <span class="badge <?= $s['badge'] ?> px-3 py-1"><?= $s['label'] ?></span>

                <?php if ($viaggio['veicolo_nome']): ?>
                    <p class="mt-3 mb-1 text-muted small">
                        <i class="fas fa-car mr-1"></i> <?= esc($viaggio['veicolo_nome']) ?>
                    </p>
                <?php endif; ?>

                <?php if ($viaggio['distanza_totale']): ?>
                    <p class="mb-1 text-muted small">
                        <i class="fas fa-road mr-1"></i>
                        <?= number_format($viaggio['distanza_totale'] / 1000, 1) ?> km —
                        <?= gmdate('H\h i\'', $viaggio['durata_totale'] ?? 0) ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="card-footer clearfix">
                <a href="<?= base_url('viaggi?data=' . $viaggio['data']) ?>"
                   class="btn btn-sm btn-secondary float-left">
                    <i class="fas fa-arrow-left mr-1"></i> Elenco
                </a>
                <a href="<?= base_url('viaggi/' . $viaggio['id'] . '/pdf') ?>" target="_blank"
                   class="btn btn-sm btn-outline-secondary float-left ml-2">
                    <i class="fas fa-print mr-1"></i> Stampa
                </a>
                <?php if ($viaggio['stato'] === 'bozza'): ?>
                    <form method="post" action="<?= base_url('viaggi/' . $viaggio['id'] . '/annulla') ?>"
                          class="float-right ml-2"
                          onsubmit="return confirm('Eliminare il viaggio? Gli interventi torneranno in coda.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash mr-1"></i> Elimina
                        </button>
                    </form>
                    <form method="post" action="<?= base_url('viaggi/' . $viaggio['id'] . '/autorizza') ?>"
                          class="float-right" onsubmit="return confirm('Approvare questo viaggio?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-check mr-1"></i> Approva
                        </button>
                    </form>
                <?php elseif ($viaggio['stato'] === 'autorizzato'): ?>
                    <form method="post" action="<?= base_url('viaggi/' . $viaggio['id'] . '/annulla') ?>"
                          class="float-right ml-2"
                          onsubmit="return confirm('Eliminare il viaggio? Gli interventi torneranno in coda.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash mr-1"></i> Elimina
                        </button>
                    </form>
                    <form method="post" action="<?= base_url('viaggi/' . $viaggio['id'] . '/riapri') ?>"
                          class="float-right"
                          onsubmit="return confirm('Riportare il viaggio in bozza? Potrà essere modificato dalla pianificazione.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-lock-open mr-1"></i> Riapri
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if (in_array($viaggio['stato'], ['bozza', 'autorizzato'])): ?>
        <div class="card card-outline card-secondary">
            <div class="card-header py-2">
                <h3 class="card-title small">
                    <i class="fas fa-car mr-1"></i> Veicolo assegnato
                </h3>
            </div>
            <div class="card-body py-2">
                <form method="post" action="<?= base_url('viaggi/' . $viaggio['id'] . '/veicolo') ?>"
                      id="form-veicolo">
                    <?= csrf_field() ?>
                    <select name="veicolo_id" id="sel-veicolo" class="form-control form-control-sm mb-2">
                        <option value="">— Nessun veicolo —</option>
                        <?php foreach ($veicoli as $v):
                            $riservato  = $veicoli_riservati[$v['id']] ?? '';
                            $impegnato  = in_array($v['id'], $veicoli_impegnati)
                                          && (int)$viaggio['veicolo_id'] !== (int)$v['id'];
                        ?>
                        <option value="<?= $v['id'] ?>"
                                <?= (int)$viaggio['veicolo_id'] === (int)$v['id'] ? 'selected' : '' ?>
                                data-riservato="<?= esc($riservato) ?>"
                                <?= $riservato ? 'style="color:#dc3545;"' : ($impegnato ? 'style="color:#6c757d;"' : '') ?>>
                            <?= esc($v['nome'] . ($v['targa'] ? ' - ' . $v['targa'] : '')) ?>
                            <?php if ($v['cambio_automatico']): ?> (aut.)<?php endif; ?>
                            <?php if ($riservato): ?> ⚠ riservato<?php endif; ?>
                            <?php if ($impegnato): ?> — già impegnato<?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Salva
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Colonna destra: tappe -->
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marked-alt mr-1"></i> Tappe
                </h3>
                <div class="card-tools">
                    <span class="badge badge-primary"><?= count($viaggio['tappe']) ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($viaggio['tappe'])): ?>
                    <div class="text-center py-4 text-muted">
                        <p>Nessuna tappa per questo viaggio.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width:32px">#</th>
                                <th>Cliente / Luogo</th>
                                <th class="d-none d-md-table-cell">Tipo</th>
                                <th class="d-none d-md-table-cell">Priorità</th>
                                <th>Stato</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($viaggio['tappe'] as $t): ?>
                            <?php
                                $nomeCliente = $t['cliente_tipo'] === 'persona_fisica'
                                    ? trim(($t['cliente_cognome'] ?? '') . ' ' . ($t['cliente_nome'] ?? ''))
                                    : ($t['ragsoc'] ?? '—');
                                $si = $stati_int[$t['intervento_stato']] ?? ['label' => $t['intervento_stato'], 'badge' => 'badge-secondary'];
                                $pr = $priorita[$t['priorita']]           ?? ['label' => $t['priorita'],           'badge' => 'badge-secondary'];
                            ?>
                            <tr>
                                <td class="align-middle text-center">
                                    <span class="badge badge-light"><?= $t['ordine'] ?></span>
                                </td>
                                <td class="align-middle">
                                    <strong><?= esc($nomeCliente) ?></strong>
                                    <?php
                                        $luogo = array_filter([$t['luogo_intervento'], $t['citta'] ?: $t['cliente_citta']]);
                                    ?>
                                    <?php if ($luogo): ?>
                                        <br><small class="text-muted"><?= esc(implode(' — ', $luogo)) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($t['descrizione'])): ?>
                                        <br><small class="text-muted" style="font-style:italic;"><?= esc(mb_strimwidth($t['descrizione'], 0, 80, '…')) ?></small>
                                    <?php endif; ?>
                                    <?php if ($t['ora_arrivo_stimata']): ?>
                                        <br><small class="text-info">
                                            <i class="fas fa-clock mr-1"></i><?= substr($t['ora_arrivo_stimata'], 0, 5) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-md-table-cell align-middle small text-muted">
                                    <?php if (!empty($t['tipo_icona'])): ?>
                                        <i class="fas <?= esc($t['tipo_icona']) ?> mr-1"></i>
                                    <?php endif; ?>
                                    <?= esc($t['tipo_nome'] ?: $t['tipo_intervento']) ?>
                                </td>
                                <td class="d-none d-md-table-cell align-middle">
                                    <span class="badge <?= $pr['badge'] ?>"><?= $pr['label'] ?></span>
                                    <?php if ($t['fissato']): ?>
                                        <i class="fas fa-lock text-muted ml-1" title="Orario fissato: <?= substr($t['ora_inizio'] ?? '', 0, 5) ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <span class="badge <?= $si['badge'] ?>"><?= $si['label'] ?></span>
                                </td>
                                <td class="align-middle text-right">
                                    <a href="<?= base_url('interventi/' . $t['intervento_id']) ?>"
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

<?= $this->section('help') ?>
<?= $this->include('help/viaggi/show') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    var form = document.getElementById('form-veicolo');
    if (! form) return;

    form.addEventListener('submit', function (e) {
        var sel      = document.getElementById('sel-veicolo');
        var opt      = sel.options[sel.selectedIndex];
        var riservato = opt ? opt.dataset.riservato : '';
        if (riservato) {
            var ok = confirm(
                '⚠ Attenzione\n\n' +
                riservato + ' ha interventi assegnati oggi e richiede il cambio automatico.\n' +
                'Questo veicolo è riservato a lui.\n\n' +
                'Vuoi assegnarlo comunque a un altro tecnico?'
            );
            if (! ok) e.preventDefault();
        }
    });
})();
</script>
<?= $this->endSection() ?>
