<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Richieste</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-inbox mr-1"></i>
            Richieste aperte
            <span class="badge badge-primary ml-1"><?= count($richieste) ?></span>
        </h3>
    </div>
    <div class="card-body p-0">
        <?php if (empty($richieste)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-check-circle fa-3x mb-3 text-success" style="opacity:.4;"></i>
            <p class="mb-0">Nessuna richiesta aperta</p>
        </div>
        <?php else: ?>
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width:90px;">Stato</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Richiedente</th>
                    <th style="width:140px;">Ricevuta</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($richieste as $r):
                $giorni   = (int) ((time() - strtotime($r['created_at'])) / 86400);
                $rigaCls  = $giorni >= 7 ? 'table-danger' : ($giorni >= 3 ? 'table-warning' : '');
                $cliente  = $r['ragsoc'] ?: trim(($r['cliente_nome'] ?? '') . ' ' . ($r['cliente_cognome'] ?? '')) ?: $r['username'];
                $statoInfo = $stati[$r['stato']] ?? ['label' => $r['stato'], 'badge' => 'badge-secondary'];
                $tipoLabel = \App\Models\RichiestaModel::TIPI[$r['tipo_intervento']] ?? $r['tipo_intervento'];
            ?>
            <tr class="<?= $rigaCls ?>" style="cursor:pointer;"
                onclick="location.href='<?= base_url('richieste/' . $r['id']) ?>'"
            >
                <td>
                    <span class="badge <?= $statoInfo['badge'] ?>"><?= $statoInfo['label'] ?></span>
                </td>
                <td>
                    <?php if ($r['cliente_id']): ?>
                    <a href="<?= base_url('clienti/' . $r['cliente_id'] . '?from=richieste') ?>" class="font-weight-bold text-dark"
                       onclick="event.stopPropagation();">
                        <?= esc($cliente) ?>
                    </a>
                    <?php else: ?>
                    <strong><?= esc($cliente) ?></strong>
                    <?php endif; ?>
                    <?php if ($r['note']): ?>
                    <small class="text-muted d-block" style="max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        <?= esc($r['note']) ?>
                    </small>
                    <?php endif; ?>
                </td>
                <td><?= esc($tipoLabel) ?></td>
                <td><?= esc($r['richiedente']) ?></td>
                <td>
                    <?php if ($giorni >= 3): ?>
                        <span class="text-<?= $giorni >= 7 ? 'danger' : 'warning' ?> font-weight-bold">
                            <i class="fas fa-clock mr-1"></i><?= $giorni ?> giorni fa
                        </span>
                    <?php else: ?>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/richieste/index') ?>
<?= $this->endSection() ?>
