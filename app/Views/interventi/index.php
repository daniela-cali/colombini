<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('interventi') ?>">Interventi</a></li>
    <li class="breadcrumb-item active">I miei interventi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
    $totale = array_sum(array_map(fn($g) => count($g['interventi']), $perTecnico))
            + count($nonAssegnati);
?>

<?php
    $qActual = array_filter($_GET, fn($k) => $k !== 'mostra_completati', ARRAY_FILTER_USE_KEY);
    $qs      = http_build_query($qActual);
    $urlBase = base_url('interventi') . ($qs ? '?' . $qs : '');
    $urlConCompletati = base_url('interventi') . '?' . http_build_query($qActual + ['mostra_completati' => '1']);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <!-- gruppo SINISTRA -->
    <div class="d-flex align-items-center" style="gap:.75rem;">
        <span class="text-muted small"><?= $totale ?> interventi</span>
        <?php if ($mostraCompletati || $statoFiltro === 'completato'): ?>
            <a href="<?= $urlBase ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-eye-slash mr-1"></i>Nascondi completati
            </a>
        <?php elseif (!$statoFiltro): ?>
            <a href="<?= $urlConCompletati ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-eye mr-1"></i>Mostra anche completati
            </a>
        <?php endif; ?>
    </div>
    <!-- gruppo DESTRA -->
    <div class="d-flex align-items-center" style="gap:.75rem;">
        <div class="input-group input-group-sm" style="width:220px;">
            <input type="search" id="cerca_interventi"
                   class="form-control"
                   placeholder="Cerca interventi...">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-search fa-fw"></i></span>
            </div>
        </div>
        <a href="<?= base_url('interventi/new') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline">Nuovo intervento</span>
            <span class="d-sm-none">Nuovo</span>
        </a>
    </div>

</div>

<?php if ($totale === 0): ?>
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
            <p>Nessun intervento ancora registrato.</p>
            <a href="<?= base_url('interventi/new') ?>" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Crea il primo intervento
            </a>
        </div>
    </div>
<?php endif; ?>

<?php
// ── Macro per la tabella interventi di un gruppo ──────────────────────────────
function _rigaIntervento(array $inv, array $tipi, array $icone, array $stati): void {
    $s       = $stati[$inv['stato']] ?? ['label' => $inv['stato'], 'badge' => 'badge-secondary'];
    $nomeCliente = $inv['cliente_ragsoc']
        ? esc($inv['cliente_ragsoc'])
        : ($inv['cliente_cognome'] ? esc($inv['cliente_cognome'] . ' ' . $inv['cliente_nome']) : '');
    $badgeEliminato = ($inv['cliente_deleted_at'] ?? null)
        ? ' <span class="badge badge-danger" title="Cliente eliminato">el.</span>'
        : '';
    $cliente = $nomeCliente
        ? $nomeCliente . $badgeEliminato
        : '<span class="text-muted">—</span>';
?>
    <tr>
        <td class="d-none d-md-table-cell text-muted small"><?= $inv['id'] ?></td>
        <td class="d-none d-lg-table-cell"><?= $cliente ?></td>
        <td>
            <i class="fas <?= esc($icone[$inv['tipo_intervento']] ?? 'fa-tools') ?> mr-1 text-muted"></i><strong><?= esc($tipi[$inv['tipo_intervento']] ?? $inv['tipo_intervento']) ?></strong>
            <?php if ($inv['luogo_intervento']): ?>
                <br><small class="text-muted"><?= esc($inv['luogo_intervento']) ?></small>
            <?php endif; ?>
            <?php if ($inv['descrizione']): ?>
                <br><small class="text-muted"><?= esc(mb_strimwidth($inv['descrizione'], 0, 80, '…')) ?></small>
            <?php endif; ?>
        </td>
        <td class="small">
            <?php if ($inv['data_pianificata']): ?>
                <?= date('d/m/Y H:i', strtotime($inv['data_pianificata'])) ?>
            <?php else: ?>
                <span class="text-muted">—</span>
            <?php endif; ?>
        </td>
        <td>
            <span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span>
        </td>
        <td class="text-right">
            <a href="<?= base_url('interventi/' . $inv['id']) ?>"
               class="btn btn-xs btn-outline-primary">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    </tr>
<?php } ?>

<?php
function _tabellaGruppo(array $interventi, array $tipi, array $icone, array $stati): void { ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th class="d-none d-md-table-cell" style="width:40px">#</th>
                    <th class="d-none d-lg-table-cell">Cliente</th>
                    <th>Tipo</th>
                    <th>Data pianificata</th>
                    <th>Stato</th>
                    <th style="width:50px"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($interventi as $inv): ?>
                    <?php _rigaIntervento($inv, $tipi, $icone, $stati); ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php foreach ($perTecnico as $gruppo):
    $colore  = esc($gruppo['info']['colore']);
    $nome    = esc($gruppo['info']['cognome'] . ' ' . $gruppo['info']['nome']);
    $nInv    = count($gruppo['interventi']);
?>
<div class="card mb-3" style="border-top: 3px solid <?= $colore ?>;">
    <div class="card-header d-flex align-items-center py-2"
         style="background: <?= $colore ?>18;">
        <span style="width:13px;height:13px;border-radius:50%;background:<?= $colore ?>;
                     display:inline-block;flex-shrink:0;margin-right:8px;"></span>
        <strong class="mr-2"><?= $nome ?></strong>
        <span class="badge badge-secondary"><?= $nInv ?></span>
    </div>
    <div class="card-body p-0">
        <?php _tabellaGruppo($gruppo['interventi'], $tipi, $icone, $stati); ?>
    </div>
</div>
<?php endforeach; ?>

<?php if (! empty($nonAssegnati)): ?>
<div class="card mb-3" style="border-top: 3px solid #adb5bd;">
    <div class="card-header d-flex align-items-center py-2" style="background:#f8f9fa;">
        <span style="width:13px;height:13px;border-radius:50%;background:#adb5bd;
                     display:inline-block;flex-shrink:0;margin-right:8px;"></span>
        <strong class="mr-2 text-muted">Non assegnati</strong>
        <span class="badge badge-secondary"><?= count($nonAssegnati) ?></span>
    </div>
    <div class="card-body p-0">
        <?php _tabellaGruppo($nonAssegnati, $tipi, $icone, $stati); ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/interventi/index') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('cerca_interventi').addEventListener('input', function(){
    const q = this.value.trim().toLowerCase();

    document.querySelectorAll('table tbody tr').forEach(
        function(tr) {
            const testo = tr.textContent.toLowerCase();
            tr.style.display = (q === '' || testo.includes(q)) ? '' : 'none';
        }
    );

    document.querySelectorAll('.card').forEach(
        function(card) {
            if (!card.querySelector('table')) return;
            const righeVisibili = card.querySelectorAll('tbody tr:not([style*="display: none"])').length;
            card.style.display = (q === '' || righeVisibili > 0) ? '' : 'none';
        }
    );
});
</script>
<?= $this->endSection() ?>