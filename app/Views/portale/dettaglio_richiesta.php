<?= $this->extend('layouts/portale') ?>

<?= $this->section('content') ?>
<div class="px-2 px-sm-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('portale') ?>" class="text-muted small">
            <i class="fas fa-arrow-left mr-1"></i> Le mie richieste
        </a>
        <?php $s = $stati[$richiesta['stato']] ?? ['label' => $richiesta['stato'], 'badge' => 'badge-secondary']; ?>
        <span class="badge <?= $s['badge'] ?> px-3 py-2"><?= $s['label'] ?></span>
    </div>

    <div class="p-card">
        <div class="p-card-header">
            <i class="fas fa-clipboard-list"></i>
            Richiesta #<?= $richiesta['id'] ?>
            <span class="font-weight-normal ml-2" style="opacity:.75; font-size:.85rem;">
                <?= date('d/m/Y H:i', strtotime($richiesta['created_at'])) ?>
            </span>
        </div>
        <div class="p-card-body">

            <div class="row mb-3">
                <div class="col-5 text-muted small font-weight-bold">Tipo</div>
                <div class="col-7 font-weight-bold">
                    <?= esc($tipi[$richiesta['tipo_intervento']] ?? $richiesta['tipo_intervento']) ?>
                </div>
            </div>

            <?php if ($richiesta['luogo_intervento']): ?>
            <div class="row mb-3">
                <div class="col-5 text-muted small font-weight-bold">Luogo</div>
                <div class="col-7"><?= esc($richiesta['luogo_intervento']) ?></div>
            </div>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-5 text-muted small font-weight-bold">Richiesto da</div>
                <div class="col-7"><?= esc($richiesta['richiedente']) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted small font-weight-bold">Telefono</div>
                <div class="col-7">
                    <a href="tel:<?= esc($richiesta['telefono_contatto']) ?>">
                        <?= esc($richiesta['telefono_contatto']) ?>
                    </a>
                </div>
            </div>

            <hr class="my-3">

            <div class="mb-1 text-muted small font-weight-bold">Descrizione problema</div>
            <p class="mb-0" style="white-space:pre-wrap; font-size:.95rem;"><?= esc($richiesta['note']) ?></p>

        </div>
        <div class="p-card-footer">
            <small class="text-muted">
                Vi contatteremo al più presto al numero indicato.
            </small>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
