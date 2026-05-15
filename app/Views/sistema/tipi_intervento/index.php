<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Tipi Intervento</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Tipi di intervento</h3>
        <a href="<?= base_url('sistema/tipi-intervento/new') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Nuovo tipo
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($tipi)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-tags fa-3x mb-3"></i>
                <p>Nessun tipo di intervento configurato.</p>
                <a href="<?= base_url('sistema/tipi-intervento/new') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Aggiungi tipo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Ordine</th>
                            <th>Codice</th>
                            <th>Icona</th>
                            <th>Nome</th>
                            <th class="d-none d-sm-table-cell">Durata default</th>
                            <th>Stato</th>
                            <th class="text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tipi as $t): ?>
                        <tr>
                            <td class="text-muted"><?= (int) $t['ordine'] ?></td>
                            <td><code><?= esc($t['codice']) ?></code></td>
                            <td class="text-center" style="width:50px;">
                                <i class="fas <?= esc($t['icona'] ?? 'fa-tools') ?> fa-lg" title="<?= esc($t['icona'] ?? '') ?>"></i>
                            </td>
                            <td><?= esc($t['nome']) ?></td>
                            <td class="d-none d-sm-table-cell"><?= (int) $t['durata_default'] ?> min</td>
                            <td>
                                <?php if ($t['attivo']): ?>
                                    <span class="badge badge-success">Attivo</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Disattivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <a href="<?= base_url('sistema/tipi-intervento/' . $t['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-outline-primary" title="Modifica">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="post"
                                      action="<?= base_url('sistema/tipi-intervento/' . $t['id'] . '/elimina') ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare il tipo «<?= esc($t['nome']) ?>»?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
