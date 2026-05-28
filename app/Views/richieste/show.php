<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('richieste') ?>">Richieste</a></li>
    <li class="breadcrumb-item active">Richiesta #<?= $richiesta['id'] ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$cliente   = $richiesta['ragsoc'] ?: trim(($richiesta['cliente_nome'] ?? '') . ' ' . ($richiesta['cliente_cognome'] ?? '')) ?: $richiesta['username'];
$statoInfo = $stati[$richiesta['stato']] ?? ['label' => $richiesta['stato'], 'badge' => 'badge-secondary'];
$tipoLabel = \App\Models\RichiestaModel::TIPI[$richiesta['tipo_intervento']] ?? $richiesta['tipo_intervento'];
$isClosed  = $richiesta['stato'] === 'chiusa';
?>

<div class="row">
    <div class="col-md-8">

        <!-- Thread -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comments mr-1"></i>
                    Thread
                </h3>
                <div class="card-tools">
                    <span class="badge <?= $statoInfo['badge'] ?> px-3 py-1"><?= $statoInfo['label'] ?></span>
                </div>
            </div>
            <div class="card-body">

                <!-- Nodo radice: richiesta originale del cliente -->
                <div class="d-flex mb-1">
                    <div class="mr-3 text-center" style="min-width:36px;">
                        <i class="fas fa-user-circle fa-2x text-secondary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <strong class="mr-2"><?= esc($cliente) ?></strong>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($richiesta['created_at'])) ?></small>
                        </div>
                        <div class="p-3 rounded" style="background:#f8f9fa; border:1px solid #dee2e6;">
                            <?= nl2br(esc($richiesta['note'] ?: '—')) ?>
                        </div>
                        <?php if ($richiesta['luogo_intervento']): ?>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-map-marker-alt mr-1"></i><?= esc($richiesta['luogo_intervento']) ?>
                        </small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Messaggi del thread -->
                <?php foreach ($messaggi as $msg):
                    $isCliente = (int) $msg['user_id'] === (int) $richiesta['user_id'];
                    if ($isCliente) {
                        $autore = $cliente;
                    } else {
                        $autore = trim(($msg['nome'] ?? '') . ' ' . ($msg['cognome'] ?? '')) ?: 'Staff';
                    }
                ?>
                <div class="d-flex mt-3 <?= $isCliente ? '' : 'ml-4' ?>">
                    <div class="mr-3 text-center" style="min-width:36px;">
                        <?php if ($isCliente): ?>
                            <i class="fas fa-user-circle fa-2x text-secondary"></i>
                        <?php else: ?>
                            <i class="fas fa-user-tie fa-2x text-primary"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <strong class="mr-2 <?= $isCliente ? '' : 'text-primary' ?>"><?= esc($autore) ?></strong>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></small>
                        </div>
                        <?php if ($isCliente): ?>
                        <div class="p-3 rounded" style="background:#f8f9fa; border:1px solid #dee2e6;">
                            <?= nl2br(esc($msg['testo'])) ?>
                        </div>
                        <?php else: ?>
                        <div class="p-3 rounded" style="background:#e8f4fd; border-left:3px solid #007bff;">
                            <?= nl2br(esc($msg['testo'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if ($isClosed): ?>
                <div class="mt-4 text-center text-muted">
                    <i class="fas fa-lock mr-1"></i> Richiesta chiusa
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Form risposta (solo se non chiusa) -->
        <?php if (! $isClosed): ?>
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-reply mr-1"></i> Rispondi</h3>
            </div>
            <form action="<?= base_url('richieste/' . $richiesta['id'] . '/risposta') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <textarea name="testo" class="form-control" rows="4"
                                  placeholder="Scrivi una risposta..."></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="mb-1 small text-muted">Cambia stato</label>
                        <select name="stato" class="form-control form-control-sm" style="max-width:220px;">
                            <option value="">— nessun cambio —</option>
                            <?php foreach ($stati as $key => $info): ?>
                            <option value="<?= $key ?>" <?= $richiesta['stato'] === $key ? 'selected' : '' ?>>
                                <?= $info['label'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="<?= base_url('richieste') ?>" class="btn btn-secondary float-left">
                        <i class="fas fa-arrow-left mr-1"></i> Elenco
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-paper-plane mr-1"></i> Invia risposta
                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="text-right mb-3">
            <a href="<?= base_url('richieste') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Torna all'elenco
            </a>
        </div>
        <?php endif; ?>

    </div>

    <!-- Sidebar dati richiesta -->
    <div class="col-md-4">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Dettagli</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="pl-3" style="width:40%;">Cliente</th>
                        <td><?= esc($cliente) ?></td>
                    </tr>
                    <tr>
                        <th class="pl-3">Tipo</th>
                        <td><?= esc($tipoLabel) ?></td>
                    </tr>
                    <tr>
                        <th class="pl-3">Richiedente</th>
                        <td><?= esc($richiesta['richiedente']) ?></td>
                    </tr>
                    <?php if ($richiesta['telefono_contatto']): ?>
                    <tr>
                        <th class="pl-3">Telefono</th>
                        <td><a href="tel:<?= esc($richiesta['telefono_contatto']) ?>"><?= esc($richiesta['telefono_contatto']) ?></a></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($richiesta['luogo_intervento']): ?>
                    <tr>
                        <th class="pl-3">Luogo</th>
                        <td><?= esc($richiesta['luogo_intervento']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th class="pl-3">Ricevuta</th>
                        <td><?= date('d/m/Y H:i', strtotime($richiesta['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th class="pl-3">Stato</th>
                        <td><span class="badge <?= $statoInfo['badge'] ?>"><?= $statoInfo['label'] ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/richieste/show') ?>
<?= $this->endSection() ?>
