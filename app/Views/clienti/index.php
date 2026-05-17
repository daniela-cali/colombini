<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Clienti</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title mb-0">Elenco clienti</h3>
        <div class="card-tools d-flex align-items-center">
            <form method="get" action="<?= base_url('clienti') ?>" class="mr-2 mb-0">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control"
                           placeholder="Cerca..." value="<?= esc($q ?? '') ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if ($q ?? ''): ?>
                            <a href="<?= base_url('clienti') ?>" class="btn btn-outline-secondary" title="Azzera">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
            <a href="<?= base_url('clienti/import') ?>" class="btn btn-outline-secondary btn-sm mr-1">
                <i class="fas fa-file-csv mr-1"></i>
                <span class="d-none d-sm-inline">Import CSV</span>
                <span class="d-sm-none">Import</span>
            </a>
            <a href="<?= base_url('clienti/new') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>
                <span class="d-none d-sm-inline">Nuovo cliente</span>
                <span class="d-sm-none">Nuovo</span>
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($clienti)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users fa-3x mb-3"></i>
                <?php if ($q ?? ''): ?>
                    <p>Nessun cliente trovato per "<strong><?= esc($q) ?></strong>".</p>
                    <a href="<?= base_url('clienti') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i> Azzera ricerca
                    </a>
                <?php else: ?>
                    <p>Nessun cliente ancora inserito.</p>
                    <a href="<?= base_url('clienti/new') ?>" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Aggiungi cliente
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Nominativo</th>
                            <th class="d-none d-md-table-cell">Tipo</th>
                            <th class="d-none d-lg-table-cell">Città</th>
                            <th class="d-none d-md-table-cell">Telefono</th>
                            <th class="d-none d-lg-table-cell">Portale</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($clienti as $c): ?>
                        <tr>
                            <td class="small text-muted align-middle"><?= esc($c['codice']) ?></td>
                            <td class="align-middle">
                                <a href="<?= base_url('clienti/' . $c['id']) ?>">
                                    <?php if ($c['tipo'] === 'persona_fisica'): ?>
                                        <?= esc(trim(($c['cognome'] ?? '') . ' ' . ($c['nome'] ?? ''))) ?>
                                    <?php else: ?>
                                        <?= esc($c['ragsoc'] ?? '') ?>
                                    <?php endif; ?>
                                </a>
                                <?php if (empty($c['geocoded_at'])): ?>
                                    <?php if ($c['geocodifica_fallita'] ?? 0): ?>
                                        <span class="badge badge-warning ml-1" title="Geocodifica fallita — indirizzo non trovato">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-danger ml-1" title="Non geocodificato — escluso dall'ottimizzazione">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($c['citta'] ?? ''): ?>
                                    <br><small class="text-muted d-lg-none"><?= esc($c['citta']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="d-none d-md-table-cell align-middle">
                                <?= $c['tipo'] === 'persona_fisica'
                                    ? '<span class="badge badge-light">Persona fisica</span>'
                                    : '<span class="badge badge-light">Società</span>' ?>
                            </td>
                            <td class="d-none d-lg-table-cell align-middle">
                                <?= esc($c['citta'] ?? '—') ?>
                                <?php if ($c['provincia'] ?? ''): ?>
                                    <span class="text-muted">(<?= esc($c['provincia']) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="d-none d-md-table-cell align-middle">
                                <?= $c['telefono'] ? esc($c['telefono']) : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td class="d-none d-lg-table-cell align-middle">
                                <?php if ($c['user_id']): ?>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Attivo</span>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right align-middle">
                                <a href="<?= base_url('clienti/' . $c['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="Scheda">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($q ?? ''): ?>
                <div class="card-footer text-muted small">
                    <?= count($clienti) ?> risultat<?= count($clienti) === 1 ? 'o' : 'i' ?>
                    per "<?= esc($q) ?>" —
                    <a href="<?= base_url('clienti') ?>">mostra tutti</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/clienti/index') ?>
<?= $this->endSection() ?>
