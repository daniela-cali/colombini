<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti') ?>">Clienti</a></li>
    <li class="breadcrumb-item active"><?= esc($nome_display) ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">

    <!-- Colonna sinistra: anagrafica -->
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-body text-center pt-4">
                <div class="mb-3">
                    <?php if ($cliente['tipo'] === 'persona_fisica'): ?>
                        <i class="fas fa-user-circle fa-5x" style="color: var(--clr-teal);"></i>
                    <?php else: ?>
                        <i class="fas fa-building fa-5x" style="color: var(--clr-teal);"></i>
                    <?php endif; ?>
                </div>
                <h4 class="mb-1"><?= esc($nome_display) ?></h4>
                <p class="text-muted small mb-2"><?= esc($cliente['codice']) ?></p>
                <span class="badge badge-light px-3 py-1">
                    <?= $cliente['tipo'] === 'persona_fisica' ? 'Persona fisica' : 'Società / Ditta' ?>
                </span>

                <?php if ($cliente['telefono']): ?>
                    <p class="mt-3 mb-1">
                        <i class="fas fa-phone mr-1 text-muted"></i>
                        <a href="tel:<?= esc($cliente['telefono']) ?>"><?= esc($cliente['telefono']) ?></a>
                    </p>
                <?php endif; ?>
                <?php if ($cliente['email']): ?>
                    <p class="mb-1">
                        <i class="fas fa-envelope mr-1 text-muted"></i>
                        <a href="mailto:<?= esc($cliente['email']) ?>"><?= esc($cliente['email']) ?></a>
                    </p>
                <?php endif; ?>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="<?= base_url('clienti') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Elenco
                </a>
                <a href="<?= base_url('clienti/' . $cliente['id'] . '/edit') ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit mr-1"></i> Modifica
                </a>
            </div>
        </div>

        <!-- Accesso portale -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-globe mr-1"></i> Accesso portale</h3>
            </div>
            <div class="card-body">
                <?php if ($utente): ?>
                    <p class="mb-1">
                        <i class="fas fa-user mr-1 text-muted"></i>
                        <strong><?= esc($utente->username) ?></strong>
                    </p>
                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Attivo</span>
                <?php else: ?>
                    <p class="text-muted small mb-2">Nessun accesso portale configurato.</p>
                    <a href="<?= base_url('clienti/' . $cliente['id'] . '/portale') ?>"
                       class="btn btn-sm btn-outline-primary btn-block">
                        <i class="fas fa-plus mr-1"></i> Crea accesso portale
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Elimina -->
        <div class="card card-outline card-danger">
            <div class="card-body text-center">
                <form method="post"
                      action="<?= base_url('clienti/' . $cliente['id'] . '/elimina') ?>"
                      onsubmit="return confirm('Eliminare il cliente <?= esc($nome_display) ?>?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger btn-sm btn-block">
                        <i class="fas fa-trash mr-1"></i> Elimina cliente
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Colonna destra: dettagli -->
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Dati anagrafici</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <?php if ($cliente['tipo'] === 'societa' && $cliente['ragsoc']): ?>
                        <tr>
                            <th class="text-muted" width="35%">Ragione sociale</th>
                            <td><?= esc($cliente['ragsoc']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['tipo'] === 'persona_fisica'): ?>
                        <?php if ($cliente['cognome']): ?>
                            <tr>
                                <th class="text-muted">Cognome</th>
                                <td><?= esc($cliente['cognome']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($cliente['nome']): ?>
                            <tr>
                                <th class="text-muted">Nome</th>
                                <td><?= esc($cliente['nome']) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($cliente['piva']): ?>
                        <tr>
                            <th class="text-muted">P.IVA</th>
                            <td><?= esc($cliente['piva']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['cfisc']): ?>
                        <tr>
                            <th class="text-muted">Cod. Fiscale</th>
                            <td><?= esc($cliente['cfisc']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['indirizzo'] || $cliente['citta']): ?>
                        <tr>
                            <th class="text-muted">Indirizzo</th>
                            <td>
                                <?= esc($cliente['indirizzo'] ?? '') ?>
                                <?php if ($cliente['citta']): ?>
                                    <br><?= esc(trim(($cliente['cap'] ? $cliente['cap'] . ' ' : '') . $cliente['citta'] . ($cliente['provincia'] ? ' (' . $cliente['provincia'] . ')' : ''))) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['telefono']): ?>
                        <tr>
                            <th class="text-muted">Telefono</th>
                            <td><a href="tel:<?= esc($cliente['telefono']) ?>"><?= esc($cliente['telefono']) ?></a></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['email']): ?>
                        <tr>
                            <th class="text-muted">Email</th>
                            <td><a href="mailto:<?= esc($cliente['email']) ?>"><?= esc($cliente['email']) ?></a></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($cliente['note']): ?>
                        <tr>
                            <th class="text-muted">Note</th>
                            <td><?= nl2br(esc($cliente['note'])) ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>