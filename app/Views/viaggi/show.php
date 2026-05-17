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
                <p class="text-muted small mb-2"><?= date('l d/m/Y', strtotime($viaggio['data'])) ?></p>
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
                <?php if ($viaggio['stato'] === 'bozza'): ?>
                    <form method="post" action="<?= base_url('viaggi/' . $viaggio['id'] . '/autorizza') ?>"
                          class="float-right" onsubmit="return confirm('Autorizzare questo viaggio?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-check mr-1"></i> Autorizza
                        </button>
                    </form>
                <?php elseif (in_array($viaggio['stato'], ['bozza', 'autorizzato'])): ?>
                    <form method="post" action="<?= base_url('viaggi/' . $viaggio['id'] . '/annulla') ?>"
                          class="float-right" onsubmit="return confirm('Annullare il viaggio? Gli interventi torneranno in coda.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-times mr-1"></i> Annulla
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($viaggio['stato'] === 'autorizzato'): ?>
        <div class="card card-outline card-danger">
            <div class="card-body py-3">
                <form method="post" action="<?= base_url('viaggi/' . $viaggio['id'] . '/annulla') ?>"
                      onsubmit="return confirm('Annullare il viaggio? Gli interventi torneranno in coda.')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger btn-sm btn-block">
                        <i class="fas fa-times mr-1"></i> Annulla viaggio
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Colonna destra: tappe -->
    <div class="col-md-8">
        <div class="card card-outline card-primary">
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
                                    <?php if ($t['posizionato_manualmente']): ?>
                                        <i class="fas fa-hand-paper text-warning ml-1" title="Posizionato manualmente"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <strong><?= esc($nomeCliente) ?></strong>
                                    <?php if ($t['luogo_intervento']): ?>
                                        <br><small class="text-muted"><?= esc($t['luogo_intervento']) ?></small>
                                    <?php endif; ?>
                                    <?php if ($t['ora_arrivo_stimata']): ?>
                                        <br><small class="text-info">
                                            <i class="fas fa-clock mr-1"></i><?= substr($t['ora_arrivo_stimata'], 0, 5) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-md-table-cell align-middle small text-muted">
                                    <?= esc($t['tipo_intervento']) ?>
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
