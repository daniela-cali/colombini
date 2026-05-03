<?= $this->extend('layouts/portale') ?>

<?= $this->section('content') ?>
<div class="px-2 px-sm-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0 font-weight-bold" style="color:var(--clr-primary);">Le mie richieste</h5>
        <span class="badge badge-pill badge-secondary"><?= count($richieste) ?></span>
    </div>

    <?php if (empty($richieste)): ?>
        <div class="p-card">
            <div class="p-card-body text-center py-5 text-muted">
                <i class="fas fa-clipboard fa-3x mb-3" style="color:var(--clr-teal);"></i>
                <p class="mb-3">Non hai ancora inviato nessuna richiesta di assistenza.</p>
                <a href="<?= base_url('portale/nuova-richiesta') ?>" class="btn btn-primary btn-block">
                    <i class="fas fa-plus mr-1"></i> Invia la prima richiesta
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($richieste as $r):
            $s = $stati[$r['stato']] ?? ['label' => $r['stato'], 'badge' => 'badge-secondary'];
        ?>
        <a href="<?= base_url('portale/richiesta/' . $r['id']) ?>" class="req-card">
            <div class="req-info">
                <h6><?= esc($tipi[$r['tipo_intervento']] ?? $r['tipo_intervento']) ?></h6>
                <small>
                    <span class="badge <?= $s['badge'] ?> mr-1"><?= $s['label'] ?></span>
                    <?= date('d/m/Y', strtotime($r['created_at'])) ?>
                    · <?= esc($r['richiedente']) ?>
                </small>
            </div>
            <i class="fas fa-chevron-right req-arrow"></i>
        </a>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
