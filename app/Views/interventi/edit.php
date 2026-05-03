<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('interventi') ?>">Interventi</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('interventi/' . $intervento['id']) ?>">#<?= $intervento['id'] ?></a></li>
    <li class="breadcrumb-item active">Modifica</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Modifica Intervento #<?= $intervento['id'] ?></h3>
                <?php $s = $stati[$intervento['stato']] ?? ['label' => $intervento['stato'], 'badge' => 'badge-secondary']; ?>
                <span class="badge <?= $s['badge'] ?> px-3 py-2"><?= $s['label'] ?></span>
            </div>
            <form method="post" action="<?= base_url('interventi/' . $intervento['id']) ?>">
                <?= csrf_field() ?>
                <div class="card-body">

                    <!-- Richiesta portale collegata -->
                    <?php if ($intervento['richiesta_id'] || !empty($richieste)): ?>
                    <div class="form-group">
                        <label>Richiesta portale collegata</label>
                        <select name="richiesta_id" class="form-control">
                            <option value="">— Nessuna —</option>
                            <?php if ($intervento['richiesta_id']): ?>
                                <option value="<?= $intervento['richiesta_id'] ?>" selected>
                                    Richiesta #<?= $intervento['richiesta_id'] ?> (attualmente collegata)
                                </option>
                            <?php endif; ?>
                            <?php foreach ($richieste ?? [] as $r): ?>
                                <?php if ($r['id'] == $intervento['richiesta_id']) continue; ?>
                                <option value="<?= $r['id'] ?>"
                                    <?= old('richiesta_id') == $r['id'] ? 'selected' : '' ?>>
                                    #<?= $r['id'] ?> — <?= esc($r['richiedente']) ?>
                                    (<?= esc($r['tipo_intervento']) ?>)
                                    — <?= date('d/m/Y', strtotime($r['created_at'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($intervento['richiesta_id']): ?>
                            <small class="form-text text-muted">
                                Cambiando selezione la richiesta precedente torna a "Nuova".
                            </small>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <?php endif; ?>

                    <div class="form-row">
                        <!-- Tipo intervento -->
                        <div class="form-group col-md-6">
                            <label>Tipo di intervento <span class="text-danger">*</span></label>
                            <select name="tipo_intervento" class="form-control" required>
                                <?php foreach ($tipi as $key => $label): ?>
                                    <option value="<?= $key ?>"
                                        <?= $intervento['tipo_intervento'] === $key ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Stato -->
                        <div class="form-group col-md-6">
                            <label>Stato</label>
                            <select name="stato" class="form-control">
                                <?php foreach ($stati as $key => $info): ?>
                                    <option value="<?= $key ?>"
                                        <?= $intervento['stato'] === $key ? 'selected' : '' ?>>
                                        <?= $info['label'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Luogo -->
                    <div class="form-group">
                        <label>Luogo intervento <small class="text-muted">(se diverso dalla sede)</small></label>
                        <input type="text" name="luogo_intervento" class="form-control"
                               value="<?= esc($intervento['luogo_intervento'] ?? '') ?>"
                               placeholder="es. Via Roma 10, Milano">
                    </div>

                    <div class="form-row">
                        <!-- Cliente -->
                        <div class="form-group col-md-6">
                            <label>Cliente</label>
                            <select name="cliente_id" class="form-control">
                                <option value="">— Nessuno —</option>
                                <?php foreach ($clienti as $c): ?>
                                    <option value="<?= $c['id'] ?>"
                                        <?= $intervento['cliente_id'] == $c['id'] ? 'selected' : '' ?>>
                                        <?= esc($c['tipo'] === 'persona_fisica'
                                            ? trim(($c['cognome'] ?? '') . ' ' . ($c['nome'] ?? ''))
                                            : ($c['ragsoc'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tecnico -->
                        <div class="form-group col-md-6">
                            <label>Tecnico assegnato</label>
                            <select name="tecnico_id" class="form-control">
                                <option value="">— Non assegnato —</option>
                                <?php foreach ($tecnici as $t): ?>
                                    <option value="<?= $t->id ?>"
                                        <?= $intervento['tecnico_id'] == $t->id ? 'selected' : '' ?>>
                                        <?= esc($t->cognome . ' ' . $t->nome) ?>
                                        <?= $t->telefono ? '(' . esc($t->telefono) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Data pianificata -->
                    <div class="form-group">
                        <label>Data e ora pianificata</label>
                        <input type="datetime-local" name="data_pianificata" class="form-control"
                               value="<?= $intervento['data_pianificata']
                                    ? date('Y-m-d\TH:i', strtotime($intervento['data_pianificata']))
                                    : '' ?>">
                    </div>

                    <!-- Descrizione -->
                    <div class="form-group">
                        <label>Descrizione / Problema</label>
                        <textarea name="descrizione" class="form-control" rows="4"
                                  maxlength="3000"><?= esc($intervento['descrizione'] ?? '') ?></textarea>
                    </div>

                    <!-- Note interne -->
                    <div class="form-group">
                        <label>Note interne <small class="text-muted">(non visibili al cliente)</small></label>
                        <textarea name="note_interne" class="form-control" rows="2"
                                  maxlength="3000"><?= esc($intervento['note_interne'] ?? '') ?></textarea>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('interventi/' . $intervento['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salva modifiche
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
