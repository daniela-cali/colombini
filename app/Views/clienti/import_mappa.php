<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti') ?>">Clienti</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('clienti/import') ?>">Import CSV</a></li>
    <li class="breadcrumb-item active">Mappa Colonne</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exchange-alt mr-1"></i> Mappa le colonne del CSV</h3>
            </div>
            <form method="post" action="<?= base_url('clienti/import/esegui') ?>">
                <?= csrf_field() ?>
                <div class="card-body p-0">

                    <div class="alert alert-info m-3 mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        Per ogni colonna del tuo CSV scegli il campo corrispondente.
                        Lascia <strong>— Non importare —</strong> per le colonne da ignorare.
                        Il mapping verrà salvato per i prossimi import.
                    </div>

                    <table class="table table-sm table-hover mb-0 mt-3">
                        <thead>
                            <tr>
                                <th class="pl-3">Colonna nel CSV</th>
                                <th>Campo nel gestionale</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($headers as $idx => $header): ?>
                            <?php
                                // Pre-seleziona da mapping salvato (per nome colonna)
                                $presel = $saved_mapping[$header] ?? '';
                                // Se non c'è nel mapping salvato, prova match diretto sul nome
                                if (! $presel && array_key_exists(strtolower($header), $campi_dest)) {
                                    $presel = strtolower($header);
                                }
                            ?>
                            <tr>
                                <td class="pl-3 align-middle">
                                    <code><?= esc($header) ?></code>
                                </td>
                                <td>
                                    <select name="mapping[<?= $idx ?>]" class="form-control form-control-sm">
                                        <option value="">— Non importare —</option>
                                        <?php foreach ($campi_dest as $campo => $label): ?>
                                            <option value="<?= $campo ?>"
                                                <?= $presel === $campo ? 'selected' : '' ?>>
                                                <?= esc($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('clienti/import') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Ricarica file
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play mr-1"></i> Avvia import
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
