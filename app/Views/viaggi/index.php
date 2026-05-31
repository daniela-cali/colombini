<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Viaggi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
    $perGiorno = [];
    //log_message('info', 'Viaggi nelle view: '. print_r($viaggi, true));
    //dd($viaggi);
    foreach ($viaggi as $v) {
        $perGiorno[$v['data']][] = $v;
    }
    //log_message('info', 'perGiorno: '.print_r($perGiorno, true));
    $periodo = data_ita($dal, false).' - '. data_ita($al, false);
?>
<!-- Navigazione periodo -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <!-- SINISTRA: filtro periodo -->
    <form method="get" action="<?= base_url('viaggi') ?> "class = "d-flex align-items-center" style="gap: .5rem; flex-wrap: nowrap;">
        <span class = "text-muted small">Periodo dal</span>
        <input class="form-control form-control-sm" type="date" name = "dal" value="<?= $dal ?>" style="width:auto" onchange="this.form.submit()" />
        <span class = "text-muted small"> - al</span>
        <input class="form-control form-control-sm" type="date" name = "al" value="<?= $al ?>" style="width:auto" onchange="this.form.submit()"/>
        <a class="btn btn-sm btn-outline-primary ml-2" href="<?= base_url('viaggi') ?>" style="width:auto; white-space:nowrap;">Torna a oggi</a>
    </form>
    <!-- DESTRA: ricerca per numero -->
    <form method="get" action="<?= base_url('viaggi') ?> "class = "d-flex align-items-center" style="gap: .5rem; flex-wrap: nowrap;">
        <div class = "input-group input-group-sm" style="width:auto">
            <div class="input-group-prepend">
                <span class="input-group-text">#</span>
            </div>
            <input type="text" name="idRicerca" class="form-control">
            <div class="input-group-append">
                <button class="btn btn-sm btn-outline-secondary" 
                    type="submit" 
                    value="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<?php if (empty($viaggi) && empty($idRicerca)): ?>
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-route fa-3x mb-3"></i>
            <p>Nessun viaggio pianificato per il periodo <strong><?= $periodo ?></strong>.</p>
        </div>
    </div>
<?php elseif (empty($viaggi) && $idRicerca): ?>
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-route fa-3x mb-3"></i>
            <p>Viaggio col numero <strong><?= $idRicerca ?></strong> non trovato.</p>
        </div>
    </div>
<?php else: ?>
    
    <?php foreach($perGiorno as $giorno => $viaggiGiorno): ?>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
               <?=  data_ita($giorno) ?>
            </h3>
            <div class="card-tools">
            <a href="<?= base_url('viaggi/pdf/' . $giorno) ?>" target="_blank"
               class="btn btn-sm">
                <i class="fas fa-print mr-1"></i> Stampa Viaggio
            </a>
                <span class="badge badge-info"><?= count($viaggiGiorno) ?></span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Tecnico</th>
                        <th>Veicolo</th>
                        <th class="d-none d-md-table-cell">Tappe</th>
                        <th class="d-none d-md-table-cell">Distanza</th>
                        <th>Stato</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($viaggiGiorno as $v): ?>
                    <?php $s = $stati[$v['stato']] ?? ['label' => $v['stato'], 'badge' => 'badge-secondary']; ?>
                    <tr>
                        <td class="align-middle">
                            #<?= esc($v['id']); ?>
                        </td>
                        <td class="align-middle">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                                background:<?= esc($v['colore'] ?? '#6c757d') ?>;margin-right:6px;"></span>
                            <?= esc($v['cognome'] . ' ' . $v['nome']) ?>
                        </td>
                        <td class="align-middle">
                            <?= $v['veicolo_nome'] ? esc($v['veicolo_nome'] . ($v['veicolo_targa'] ? ' - ' . $v['veicolo_targa'] : '')) : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td class="d-none d-md-table-cell align-middle text-muted small">
                            <?= (int)$v['tappe_count'] > 0 ? (int)$v['tappe_count'] : '—' ?>
                        </td>
                        <td class="d-none d-md-table-cell align-middle text-muted small">
                            <?= $v['distanza_totale'] ? number_format($v['distanza_totale'] / 1000, 1) . ' km' : '—' ?>
                        </td>
                        <td class="align-middle">
                            <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
                        </td>
                        <td class="text-right align-middle" style="white-space:nowrap;">
                            <a href="<?= base_url('viaggi/' . $v['id']) ?>"
                               class="btn btn-sm btn-outline-primary mr-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="post" action="<?= base_url('viaggi/' . $v['id'] . '/annulla') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Eliminare il viaggio di <?= esc(addslashes($v['cognome'] . ' ' . $v['nome'])) ?>?\nGli interventi torneranno in coda.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        title="Elimina e ripristina interventi">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>



<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/viaggi/index') ?>
<?= $this->endSection() ?>
