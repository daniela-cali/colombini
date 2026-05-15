<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item active">Dashboard</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Widgets contatori -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= $stats['clienti'] ?? 0 ?></h3>
                <p>Clienti</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="<?= base_url('clienti') ?>" class="small-box-footer">
                Dettaglio <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $stats['impianti'] ?? 0 ?></h3>
                <p>Impianti Attivi</p>
            </div>
            <div class="icon"><i class="fas fa-swimming-pool"></i></div>
            <a href="<?= base_url('impianti') ?>" class="small-box-footer">
                Dettaglio <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $stats['interventi_aperti'] ?? 0 ?></h3>
                <p>Interventi Aperti</p>
            </div>
            <div class="icon"><i class="fas fa-tools"></i></div>
            <a href="<?= base_url('interventi') ?>" class="small-box-footer">
                Dettaglio <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $stats['interventi_mese'] ?? 0 ?></h3>
                <p>Interventi Questo Mese</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="<?= base_url('report') ?>" class="small-box-footer">
                Report <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Riepilogo tecnici -->
<?php if (!empty($riepilogo_tecnici)): ?>
<div class="row">
    <?php foreach ($riepilogo_tecnici as $t):
        $clr = esc($t['colore'] ?? '#3b82f6');
    ?>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card" style="border-top:3px solid <?= $clr ?>;">
            <div class="card-header p-2" style="background:<?= $clr ?>18;">
                <div class="d-flex align-items-center">
                    <span style="width:12px;height:12px;border-radius:50%;background:<?= $clr ?>;display:inline-block;flex-shrink:0;margin-right:8px;"></span>
                    <a href="<?= base_url('sistema/tecnici/' . $t['id']) ?>"
                       class="font-weight-bold text-truncate text-dark">
                        <?= esc($t['cognome'] . ' ' . $t['nome']) ?>
                    </a>
                    <?php if ((int)$t['aperti'] > 0): ?>
                        <span class="badge badge-warning ml-auto"><?= (int)$t['aperti'] ?></span>
                    <?php else: ?>
                        <span class="badge badge-success ml-auto"><i class="fas fa-check"></i></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between py-1 px-3">
                        <span class="text-muted">Pianificati</span>
                        <strong><?= (int)$t['pianificati'] ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-1 px-3">
                        <span class="text-muted">In corso</span>
                        <strong><?= (int)$t['in_corso'] ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-1 px-3">
                        <span class="text-muted">Completati mese</span>
                        <strong><?= (int)$t['completati_mese'] ?></strong>
                    </li>
                </ul>
            </div>
            <div class="card-footer p-1 text-right">
                <a href="<?= base_url('sistema/tecnici/' . $t['id']) ?>" class="btn btn-xs btn-outline-primary">
                    <i class="fas fa-eye mr-1"></i>Scheda
                </a>
                <a href="<?= base_url('interventi/new?tecnico_id=' . $t['id']) ?>"
                   class="btn btn-xs btn-outline-success">
                    <i class="fas fa-plus mr-1"></i>Intervento
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Ultime richieste portale -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-inbox mr-1"></i> Ultime richieste portale
                </h3>
                <?php $nuove = array_filter($ultime_richieste ?? [], fn($r) => $r['stato'] === 'nuova'); ?>
                <?php if (count($nuove)): ?>
                    <span class="badge badge-info"><?= count($nuove) ?> nuove</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($ultime_richieste)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">Nessuna richiesta dal portale.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Richiedente</th>
                                    <th>Cliente</th>
                                    <th class="d-none d-md-table-cell">Tipo</th>
                                    <th class="d-none d-sm-table-cell">Data</th>
                                    <th>Stato</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($ultime_richieste as $r): ?>
                                <tr>
                                    <td><?= esc($r['richiedente']) ?></td>
                                    <td>
                                        <?php
                                            if (($r['cliente_tipo'] ?? '') === 'persona_fisica') {
                                                echo esc(trim(($r['cliente_cognome'] ?? '') . ' ' . ($r['cliente_nome'] ?? '')));
                                            } elseif ($r['ragsoc'] ?? '') {
                                                echo esc($r['ragsoc']);
                                            } else {
                                                echo '<span class="text-muted">' . esc($r['username'] ?? '—') . '</span>';
                                            }
                                        ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?= esc($stati_richiesta[$r['tipo_intervento']] ?? $r['tipo_intervento']) ?>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-muted small">
                                        <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                                    </td>
                                    <td>
                                        <?php $sr = $stati_richiesta[$r['stato']] ?? ['label' => $r['stato'], 'badge' => 'badge-secondary']; ?>
                                        <span class="badge <?= $sr['badge'] ?>"><?= $sr['label'] ?></span>
                                    </td>
                                    <td class="text-right">
                                        <?php if ($r['stato'] === 'nuova'): ?>
                                            <a href="<?= base_url('interventi/new?richiesta_id=' . $r['id']) ?>"
                                               class="btn btn-xs btn-outline-success" title="Crea intervento">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        <?php endif; ?>
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

<!-- Ultimi interventi + preventivi in attesa -->
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-tools mr-1"></i> Ultimi Interventi
                </h3>
                <a href="<?= base_url('interventi') ?>" class="btn btn-sm btn-primary">
                    Vedi tutti
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($ultimi_interventi)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <p class="mb-0">Nessun intervento registrato.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Cliente</th>
                                    <th class="d-none d-md-table-cell">Tecnico</th>
                                    <th class="d-none d-sm-table-cell">Data</th>
                                    <th>Stato</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($ultimi_interventi as $inv): ?>
                                <tr>
                                    <td><?= esc($tipi[$inv['tipo_intervento']] ?? $inv['tipo_intervento']) ?></td>
                                    <td>
                                        <?php
                                            if ($inv['cliente_ragsoc'] ?? '') {
                                                echo esc($inv['cliente_ragsoc']);
                                            } elseif ($inv['cliente_cognome'] ?? '') {
                                                echo esc(trim($inv['cliente_cognome'] . ' ' . ($inv['cliente_nome'] ?? '')));
                                            } else {
                                                echo '<span class="text-muted">—</span>';
                                            }
                                        ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?= ($inv['tecnico_cognome'] ?? '')
                                            ? esc($inv['tecnico_cognome'] . ' ' . ($inv['tecnico_nome'] ?? ''))
                                            : '<span class="text-muted small">Non assegnato</span>' ?>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-muted small">
                                        <?= ($inv['data_pianificata'] ?? '')
                                            ? date('d/m/Y', strtotime($inv['data_pianificata']))
                                            : '—' ?>
                                    </td>
                                    <td>
                                        <?php $si = $stati_intervento[$inv['stato']] ?? ['label' => $inv['stato'], 'badge' => 'badge-secondary']; ?>
                                        <span class="badge <?= $si['badge'] ?>"><?= $si['label'] ?></span>
                                    </td>
                                    <td class="text-right" style="min-width:160px">
                                        <?php if (! ($inv['tecnico_id'] ?? null)): ?>
                                            <form method="post" action="<?= base_url('interventi/' . $inv['id'] . '/assegna') ?>"
                                                  class="d-inline-flex align-items-center">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="from" value="dashboard">
                                                <select name="tecnico_id" class="form-control form-control-sm mr-1"
                                                        style="max-width:130px">
                                                    <option value="">— Assegna —</option>
                                                    <?php foreach ($tecnici ?? [] as $t): ?>
                                                        <option value="<?= $t->id ?>">
                                                            <?= esc($t->cognome . ' ' . $t->nome) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-success" title="Assegna">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="<?= base_url('interventi/' . $inv['id']) ?>"
                                               class="btn btn-xs btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
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

    <div class="col-md-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice mr-1"></i> Preventivi in Attesa
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if (!empty($preventivi_attesa ?? [])): ?>
                        <?php foreach ($preventivi_attesa as $p): ?>
                        <li class="item">
                            <div class="product-info">
                                <a href="<?= base_url('preventivi/' . $p['id']) ?>" class="product-title">
                                    <?= esc($p['cliente']) ?>
                                    <span class="badge badge-warning float-right">
                                        <?= esc($p['importo']) ?> €
                                    </span>
                                </a>
                                <span class="product-description"><?= esc($p['descrizione']) ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="item">
                            <div class="product-info text-muted text-center py-2">
                                Nessun preventivo in attesa
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="<?= base_url('preventivi') ?>">Tutti i preventivi</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>