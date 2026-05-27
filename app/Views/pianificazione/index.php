<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Pianificazione</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/pianificazione.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$prevSett   = date('Y-m-d', strtotime($lunedi . ' -7 days'));
$nextSett   = date('Y-m-d', strtotime($lunedi . ' +7 days'));
$oggi       = date('Y-m-d');
$giorniNomi = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];

// --- Closure: card nel pool ---
$renderPoolCard = function (array $i) use ($tipiPerCodice, $priorita, $tecnici, $competenzePerTecnico): void {
    $tipoInfo = $tipiPerCodice[$i['tipo_intervento']] ?? ['id' => 0, 'nome' => $i['tipo_intervento'], 'icona' => 'fa-wrench', 'durata_default' => 60];
    $priInfo  = $priorita[$i['priorita']] ?? ['label' => $i['priorita'], 'badge' => 'badge-secondary'];
    $nomeCliente = '';
    if (!empty($i['cliente_tipo'])) {
        $nomeCliente = $i['cliente_tipo'] === 'persona_fisica'
            ? trim(($i['cliente_cognome'] ?? '') . ' ' . ($i['cliente_nome'] ?? ''))
            : ($i['ragsoc'] ?? '');
    }
    $adatti = [];
    foreach ($tecnici as $t) {
        if (($competenzePerTecnico[$t->id][$tipoInfo['id']] ?? 0) >= 2) {
            $adatti[] = $t;
        }
    }
    $descr        = $i['descrizione'] ?? '';
    $luogo        = $i['luogo_intervento'] ?? '';
    $citta        = $i['citta'] ?? '';
    $luogoDisplay = $citta ?: $luogo;
    ?>
    <div class="intervention-card <?= esc($i['priorita']) ?>"
         draggable="true"
         data-id="<?= $i['id'] ?>"
         data-modal="1"
         data-tc="<?= esc($i['tipo_intervento']) ?>"
         data-cid="<?= (int)($i['cliente_id'] ?? 0) ?>"
         data-durata="<?= (int)$tipoInfo['durata_default'] ?>"
         data-mc="<?= htmlspecialchars($nomeCliente, ENT_QUOTES) ?>"
         data-mt="<?= htmlspecialchars($tipoInfo['nome'], ENT_QUOTES) ?>"
         data-mi="<?= htmlspecialchars($tipoInfo['icona'], ENT_QUOTES) ?>"
         data-mp="<?= htmlspecialchars($priInfo['label'], ENT_QUOTES) ?>"
         data-mb="<?= htmlspecialchars($priInfo['badge'], ENT_QUOTES) ?>"
         data-ml="<?= htmlspecialchars($luogo, ENT_QUOTES) ?>"
         data-mci="<?= htmlspecialchars($citta, ENT_QUOTES) ?>"
         data-md="<?= htmlspecialchars($descr, ENT_QUOTES) ?>"
         data-link="<?= base_url('interventi/' . $i['id']) ?>">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <div class="d-flex align-items-center" style="gap:.35rem;">
                <span class="badge <?= esc($priInfo['badge']) ?>" style="font-size:.68rem;"><?= esc($priInfo['label']) ?></span>
                <small class="text-muted" title="<?= esc($tipoInfo['nome']) ?>">
                    <i class="fas <?= esc($tipoInfo['icona']) ?>"></i>
                </small>
            </div>
            <small class="text-muted">#<?= $i['id'] ?></small>
        </div>
        <div class="font-weight-bold small mb-1 text-truncate">
            <?= $nomeCliente ? esc($nomeCliente) : '<em class="text-muted">Senza cliente</em>' ?>
        </div>
        <?php if ($luogoDisplay): ?>
        <div class="small text-muted mb-1">
            <i class="fas fa-map-marker-alt mr-1"></i><?= esc($luogoDisplay) ?>
        </div>
        <?php endif; ?>
        <?php if ($descr): ?>
        <div class="small text-muted card-descr-preview">
            <?= esc(mb_strimwidth($descr, 0, 60, '…')) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($adatti)): ?>
        <div class="mt-1">
            <small class="text-muted">Consigliati: </small>
            <?php foreach ($adatti as $at):
                $ini = strtoupper(substr($at->nome, 0, 1) . substr($at->cognome, 0, 1));
            ?>
                <span class="tech-dot" style="background:<?= esc($at->colore ?? '#6c757d') ?>"
                      title="<?= esc($at->cognome . ' ' . $at->nome) ?>"><?= $ini ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
};

// --- Closure: card nel giorno (griglia settimanale) ---
$renderDayCard = function (array $i) use ($tipiPerCodice, $priorita): void {
    $tipoInfo = $tipiPerCodice[$i['tipo_intervento']] ?? ['id' => 0, 'nome' => $i['tipo_intervento'], 'icona' => 'fa-wrench', 'durata_default' => 60];
    $priInfo  = $priorita[$i['priorita']] ?? ['label' => $i['priorita'], 'badge' => 'badge-secondary'];
    $nomeCliente = '';
    if (!empty($i['cliente_tipo'])) {
        $nomeCliente = $i['cliente_tipo'] === 'persona_fisica'
            ? trim(($i['cliente_cognome'] ?? '') . ' ' . ($i['cliente_nome'] ?? ''))
            : ($i['ragsoc'] ?? '');
    }
    $luogo     = $i['luogo_intervento'] ?? '';
    $citta     = $i['citta'] ?? '';
    $descr     = $i['descrizione'] ?? '';
    $ora       = $i['data_pianificata'] ? date('H:i', strtotime($i['data_pianificata'])) : '';
    $tecNome   = trim(($i['tecnico_cognome'] ?? '') . ' ' . ($i['tecnico_nome'] ?? ''));
    $tecColore = $i['tecnico_colore'] ?? '#6c757d';
    $tecIni    = $tecNome
        ? strtoupper(substr($i['tecnico_nome'] ?? '', 0, 1) . substr($i['tecnico_cognome'] ?? '', 0, 1))
        : '';
    $canRevert = ($i['stato'] === 'pianificato');
    ?>
    <div class="intervention-card <?= esc($i['priorita']) ?> mb-1"
         data-id="<?= $i['id'] ?>"
         data-saved="1"
         data-modal="1"
         data-tc="<?= esc($i['tipo_intervento']) ?>"
         data-cid="<?= (int)($i['cliente_id'] ?? 0) ?>"
         data-mc="<?= htmlspecialchars($nomeCliente, ENT_QUOTES) ?>"
         data-mt="<?= htmlspecialchars($tipoInfo['nome'], ENT_QUOTES) ?>"
         data-mi="<?= htmlspecialchars($tipoInfo['icona'], ENT_QUOTES) ?>"
         data-mp="<?= htmlspecialchars($priInfo['label'], ENT_QUOTES) ?>"
         data-mb="<?= htmlspecialchars($priInfo['badge'], ENT_QUOTES) ?>"
         data-ml="<?= htmlspecialchars($luogo, ENT_QUOTES) ?>"
         data-mci="<?= htmlspecialchars($citta, ENT_QUOTES) ?>"
         data-md="<?= htmlspecialchars($descr, ENT_QUOTES) ?>"
         data-link="<?= base_url('interventi/' . $i['id']) ?>">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <div class="d-flex align-items-center" style="gap:.3rem;">
                <span class="badge <?= esc($priInfo['badge']) ?>" style="font-size:.6rem;"><?= esc($priInfo['label']) ?></span>
                <small class="text-muted" title="<?= esc($tipoInfo['nome']) ?>">
                    <i class="fas <?= esc($tipoInfo['icona']) ?>"></i>
                </small>
            </div>
            <?php if ($canRevert): ?>
                <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-rimuovi-giornata"
                        title="Rimuovi dalla pianificazione" style="line-height:1;">
                    <i class="fas fa-times"></i>
                </button>
            <?php endif; ?>
        </div>
        <div class="small font-weight-bold text-truncate">
            <?= $nomeCliente ? esc($nomeCliente) : '<em class="text-muted">—</em>' ?>
        </div>
        <?php if ($citta || $luogo): ?>
        <div class="small text-muted text-truncate" style="font-size:.7rem;">
            <i class="fas fa-map-marker-alt mr-1"></i><?= esc($citta ?: $luogo) ?>
        </div>
        <?php endif; ?>
        <?php if ($tecNome): ?>
        <div class="d-flex align-items-center mt-1" style="gap:.25rem;">
            <span class="tech-dot" style="background:<?= esc($tecColore) ?>;width:16px;height:16px;font-size:.45rem;"><?= esc($tecIni) ?></span>
            <small class="text-muted" style="font-size:.68rem;"><?= esc($tecNome) ?></small>
        </div>
        <?php endif; ?>
    </div>
    <?php
};
?>

<!-- Toolbar -->
<div class="card card-outline card-primary mb-3">
    <div class="card-header py-2">
        <h3 class="card-title">
            <i class="fas fa-calendar-week mr-1"></i>
            Settimana del <?= date('d/m/Y', strtotime($lunedi)) ?>
        </h3>
        <div class="card-tools d-flex align-items-center" style="gap:.4rem;">
            <a href="<?= base_url('pianificazione?data=' . $prevSett) ?>" class="btn btn-sm btn-outline-secondary"
               title="Settimana precedente"><i class="fas fa-chevron-left"></i></a>
            <input type="date" id="data-sel" class="form-control form-control-sm" style="width:145px;"
                   value="<?= esc($data) ?>"
                   onchange="window.location.href='<?= base_url('pianificazione') ?>?data='+this.value">
            <a href="<?= base_url('pianificazione?data=' . $nextSett) ?>" class="btn btn-sm btn-outline-secondary"
               title="Settimana successiva"><i class="fas fa-chevron-right"></i></a>
            <a href="<?= base_url('pianificazione?data=' . $oggi) ?>" class="btn btn-sm btn-outline-primary">Oggi</a>
        </div>
    </div>
    <div id="alert-salva-wrap" style="display:none;">
        <div class="card-body py-1 px-2">
            <div id="alert-salva" class="alert py-2 mb-0"></div>
        </div>
    </div>
</div>

<!-- Layout -->
<div class="planning-layout">

    <!-- Pool sidebar -->
    <div class="pool-panel">
        <div class="card card-outline card-primary">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fas fa-inbox mr-1 text-primary"></i> Da pianificare
                    <span class="badge badge-primary ml-1"><?= count($interventi) ?></span>
                </h3>
            </div>
            <div class="card-body p-2">
                <?php if (empty($interventi)): ?>
                    <div class="pool-empty">
                        <i class="fas fa-check-circle fa-2x d-block mb-2 text-success" style="opacity:.5;"></i>
                        Tutti pianificati
                    </div>
                <?php else: ?>
                    <?php foreach ($poolPerTipo as $tipo => $invPerTipo):
                        $tipoInfo   = $tipiPerCodice[$tipo] ?? ['nome' => $tipo, 'icona' => 'fa-wrench'];
                        $collapseId = 'pool-tipo-' . preg_replace('/\W/', '_', $tipo);
                    ?>
                    <div class="mb-1">
                        <a class="d-flex align-items-center justify-content-between px-2 py-1 text-dark"
                           data-toggle="collapse" href="#<?= $collapseId ?>"
                           style="background:#f4f6f9;border-radius:4px;text-decoration:none;">
                            <span class="small font-weight-bold">
                                <i class="fas <?= esc($tipoInfo['icona']) ?> mr-1 text-muted"></i>
                                <?= esc($tipoInfo['nome']) ?>
                            </span>
                            <span class="badge badge-secondary"><?= count($invPerTipo) ?></span>
                        </a>
                        <div class="collapse show pool-zone" id="<?= $collapseId ?>">
                            <?php foreach ($invPerTipo as $i): $renderPoolCard($i); endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Griglia settimanale -->
    <div class="week-panel">
        <div class="week-grid-wrap">
            <div class="week-grid">
                <?php foreach ($settimana as $idx => $g):
                    $isOggi    = $g['data'] === $oggi;
                    $isWeekend = $idx >= 5;
                    $colClass  = 'day-col' . ($isOggi ? ' oggi' : '') . ($isWeekend ? ' weekend' : '');
                ?>
                <div class="<?= $colClass ?>">
                    <div class="day-header">
                        <div class="font-weight-bold"><?= $giorniNomi[$idx] ?></div>
                        <div style="font-size:.72rem;"><?= date('d/m', strtotime($g['data'])) ?></div>
                        <?php if ($g['count'] > 0): ?>
                            <span class="badge badge-success mt-1" style="font-size:.58rem;"><?= $g['count'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="day-drop-zone" data-date="<?= $g['data'] ?>">
                        <?php foreach ($giornataPerGiorno[$g['data']] ?? [] as $i): $renderDayCard($i); endforeach; ?>
                        <div class="day-empty<?= empty($giornataPerGiorno[$g['data']]) ? '' : ' d-none' ?>">
                            <i class="fas fa-arrow-down"></i><br>Trascina qui
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<!-- Modal dettaglio intervento (click su card) -->
<div class="modal fade" id="modal-intervento" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title d-flex align-items-center">
                    <span id="mi-badge" class="badge mr-2"></span>
                    <i id="mi-icona" class="fas fa-wrench mr-1"></i>
                    <span id="mi-tipo"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <dl class="mb-0">
                    <dt class="small text-muted">Cliente</dt>
                    <dd id="mi-cliente" class="mb-2 font-weight-bold"></dd>
                    <dt class="small text-muted" id="mi-luogo-label" style="display:none;">Luogo</dt>
                    <dd id="mi-luogo" class="mb-2" style="display:none;"></dd>
                    <dt class="small text-muted" id="mi-descr-label" style="display:none;">Descrizione</dt>
                    <dd id="mi-descr" class="mb-0" style="display:none;"></dd>
                </dl>
            </div>
            <div class="modal-footer py-2">
                <a id="mi-link" href="#" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-external-link-alt mr-1"></i> Apri scheda
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal assegna tecnico (al drop su giorno) -->
<div class="modal fade" id="modal-assegna" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success py-2">
                <h5 class="modal-title text-white">
                    <i class="fas fa-calendar-day mr-1"></i>
                    Pianifica — <span id="assegna-giorno" class="font-weight-bold"></span>
                </h5>
            </div>
            <div class="modal-body">
                <p class="mb-0 font-weight-bold" id="assegna-cliente"></p>
                <p class="small text-muted mb-1" id="assegna-tipo"></p>
                <p class="small text-muted fst-italic mb-3" id="assegna-descr" style="display:none;"></p>
                <div class="form-group mb-0">
                    <label class="small">Tecnico <span class="text-muted font-weight-normal">(opzionale)</span></label>
                    <select id="assegna-tecnico" class="form-control form-control-sm">
                        <option value="">— Non assegnato —</option>
                    </select>
                    <div id="assegna-suggerimento" class="mt-1"></div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-assegna-annulla">Annulla</button>
                <button type="button" class="btn btn-success btn-sm" id="btn-assegna-conferma">
                    <i class="fas fa-check mr-1"></i> Pianifica
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/pianificazione/index') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    var csrfToken   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var oraInizio   = '<?= esc($oraInizio) ?>';
    var alertWrapEl = document.getElementById('alert-salva-wrap');
    var alertEl     = document.getElementById('alert-salva');
    var dragging    = false;
    var dragCard    = null;
    var pendingDate = null;

    var giorniIt = ['', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];

    // Dati competenze per popolare il select tecnico
    var tecnici              = <?= json_encode(array_map(fn($t) => ['id' => $t->id, 'nome' => $t->nome, 'cognome' => $t->cognome], $tecnici)) ?>;
    var competenzePerTecnico = <?= json_encode($competenzePerTecnico) ?>;
    var tipiPerCodice        = <?= json_encode(array_map(fn($t) => ['id' => $t['id']], $tipiPerCodice)) ?>;
    var livelliNomi          = {1: 'Base', 2: 'Autonomo', 3: 'Referente'};

    function buildTecnicoSelect(tipoCode) {
        var tipoId    = (tipiPerCodice[tipoCode] || {}).id || 0;
        var qualificati = [], base = [], altri = [];

        tecnici.forEach(function (t) {
            var livello = ((competenzePerTecnico[t.id] || {})[tipoId]) || 0;
            if      (livello >= 2) qualificati.push({t: t, livello: livello});
            else if (livello === 1) base.push({t: t, livello: livello});
            else                    altri.push({t: t});
        });

        var html = '<option value="">— Non assegnato —</option>';

        if (qualificati.length) {
            html += '<optgroup label="Autonomi / Referenti">';
            qualificati.forEach(function (item) {
                html += '<option value="' + item.t.id + '">'
                      + item.t.cognome + ' ' + item.t.nome
                      + ' — ' + livelliNomi[item.livello] + '</option>';
            });
            html += '</optgroup>';
        }
        if (base.length) {
            html += '<optgroup label="Base">';
            base.forEach(function (item) {
                html += '<option value="' + item.t.id + '">'
                      + item.t.cognome + ' ' + item.t.nome + ' — Base</option>';
            });
            html += '</optgroup>';
        }
        if (altri.length) {
            html += '<optgroup label="Non competenti">';
            altri.forEach(function (item) {
                html += '<option value="' + item.t.id + '" style="color:#adb5bd;">'
                      + item.t.cognome + ' ' + item.t.nome + '</option>';
            });
            html += '</optgroup>';
        }

        assegnaTecnicoEl.innerHTML = html;
    }

    // --- Drag dal pool ---
    document.querySelectorAll('.pool-zone .intervention-card').forEach(function (card) {
        card.addEventListener('dragstart', function (e) {
            dragging = true;
            dragCard = card;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.dataset.id);
            setTimeout(function () { card.classList.add('dragging'); }, 0);
        });
        card.addEventListener('dragend', function () {
            card.classList.remove('dragging');
            dragging = false;
        });
    });

    // --- Drop sulle colonne dei giorni ---
    document.querySelectorAll('.day-drop-zone').forEach(function (zone) {
        zone.addEventListener('dragenter', function (e) {
            if (!dragCard) return;
            e.preventDefault();
            zone.classList.add('drag-over');
        });
        zone.addEventListener('dragover', function (e) {
            if (!dragCard) return;
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });
        zone.addEventListener('dragleave', function (e) {
            if (!zone.contains(e.relatedTarget)) {
                zone.classList.remove('drag-over');
            }
        });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('drag-over');
            if (!dragCard) return;
            pendingDate = zone.dataset.date;
            showModalAssegna(dragCard, pendingDate);
        });
    });

    // --- Modal assegna ---
    var $modalAssegna    = $('#modal-assegna');
    var assegnaTecnicoEl = document.getElementById('assegna-tecnico');
    var assegnaSuggEl    = document.getElementById('assegna-suggerimento');

    function showModalAssegna(card, dateStr) {
        var d    = new Date(dateStr + 'T12:00:00');
        var dow  = d.getDay() === 0 ? 7 : d.getDay();
        var label = giorniIt[dow] + ' ' +
                    String(d.getDate()).padStart(2, '0') + '/' +
                    String(d.getMonth() + 1).padStart(2, '0');

        document.getElementById('assegna-giorno').textContent  = label;
        document.getElementById('assegna-cliente').textContent = card.dataset.mc || '—';
        document.getElementById('assegna-tipo').textContent    = card.dataset.mt || '';
        var descrEl = document.getElementById('assegna-descr');
        descrEl.textContent   = card.dataset.md || '';
        descrEl.style.display = card.dataset.md ? '' : 'none';
        buildTecnicoSelect(card.dataset.tc || '');
        assegnaSuggEl.innerHTML = '';

        var tipo      = card.dataset.tc  || '';
        var clienteId = card.dataset.cid || 0;
        if (tipo) {
            var apiUrl = '<?= base_url('interventi/api/tecnico-consigliato') ?>'
                       + '?tipo_intervento=' + encodeURIComponent(tipo)
                       + '&cliente_id='      + encodeURIComponent(clienteId)
                       + '&data='            + encodeURIComponent(dateStr);

            fetch(apiUrl)
                .then(function (risposta) {
                    return risposta.json();
                })
                .then(function (json) {
                    if (!json.tecnico) return;

                    var t = json.tecnico;
                    var dettaglio = json.source === 'referente' ? 'Referente'
                                  : json.source === 'storico'   ? t.cnt + ' int. simili'
                                  :                               'disponibile';

                    assegnaSuggEl.innerHTML =
                        '<div class="alert alert-info py-1 px-2 mb-0 small">'
                        + '<i class="fas fa-lightbulb mr-1"></i>'
                        + 'Consigliato: <strong>' + t.cognome + ' ' + t.nome + '</strong> '
                        + '<span class="text-muted">(' + dettaglio + ')</span>'
                        + ' <a href="#" class="ml-2"'
                        + ' onclick="document.getElementById(\'assegna-tecnico\').value=\'' + t.tecnico_id + '\';'
                        + 'this.parentElement.remove();return false;">Usa questo</a>'
                        + '</div>';
                })
                .catch(function () {});
        }

        $modalAssegna.modal('show');
    }

    $modalAssegna.on('hide.bs.modal', function () {
        dragCard    = null;
        pendingDate = null;
    });

    document.getElementById('btn-assegna-annulla').addEventListener('click', function () {
        $modalAssegna.modal('hide');
    });

    // Conferma → AJAX pianifica
    document.getElementById('btn-assegna-conferma').addEventListener('click', function () {
        if (!dragCard || !pendingDate) return;

        var card       = dragCard;
        var tecnicoId  = assegnaTecnicoEl.value || '';
        var dataPianif = pendingDate + 'T' + oraInizio;
        var btnConf    = this;

        btnConf.disabled  = true;
        btnConf.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Salvo…';

        var formData = new FormData();
        formData.append('data_pianificata', dataPianif);
        if (tecnicoId) formData.append('tecnico_id', tecnicoId);

        fetch('<?= base_url('interventi/') ?>' + card.dataset.id + '/pianifica', {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body:    formData,
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            if (json.ok) {
                dragCard    = null;
                pendingDate = null;
                $modalAssegna.modal('hide');
                window.location.reload();
            } else {
                btnConf.disabled  = false;
                btnConf.innerHTML = '<i class="fas fa-check mr-1"></i> Pianifica';
                showAlert('danger', json.msg || 'Errore durante il salvataggio.');
            }
        })
        .catch(function () {
            btnConf.disabled  = false;
            btnConf.innerHTML = '<i class="fas fa-check mr-1"></i> Pianifica';
            showAlert('danger', 'Errore di rete.');
        });
    });

    // --- X button (rimuovi pianificazione) ---
    document.querySelectorAll('.btn-rimuovi-giornata').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var card = this.closest('.intervention-card');
            if (!card) return;
            card.style.opacity = '.4';
            fetch('<?= base_url('interventi/') ?>' + card.dataset.id + '/annulla-pianificazione', {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
            })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (json.ok) {
                    window.location.reload();
                } else {
                    card.style.opacity = '';
                    showAlert('danger', json.msg || 'Errore.');
                }
            })
            .catch(function () {
                card.style.opacity = '';
                showAlert('danger', 'Errore di rete.');
            });
        });
    });

    // --- Click su card → modal dettaglio ---
    document.addEventListener('click', function (e) {
        if (dragging) return;
        if (e.target.closest('.btn-rimuovi-giornata')) return;
        var card = e.target.closest('.intervention-card[data-modal]');
        if (!card) return;

        var luogo = [card.dataset.ml, card.dataset.mci].filter(Boolean).join(' — ');
        var descr = card.dataset.md || '';

        document.getElementById('mi-badge').className   = 'badge mr-2 ' + (card.dataset.mb || 'badge-secondary');
        document.getElementById('mi-badge').textContent = card.dataset.mp || '';
        document.getElementById('mi-icona').className   = 'fas ' + (card.dataset.mi || 'fa-wrench') + ' mr-1';
        document.getElementById('mi-tipo').textContent  = card.dataset.mt || '';
        document.getElementById('mi-cliente').textContent = card.dataset.mc || '—';

        var luogoLabel = document.getElementById('mi-luogo-label');
        var luogoEl    = document.getElementById('mi-luogo');
        luogoLabel.style.display = luogo ? '' : 'none';
        luogoEl.style.display    = luogo ? '' : 'none';
        luogoEl.textContent      = luogo;

        var descrLabel = document.getElementById('mi-descr-label');
        var descrEl    = document.getElementById('mi-descr');
        descrLabel.style.display = descr ? '' : 'none';
        descrEl.style.display    = descr ? '' : 'none';
        descrEl.textContent      = descr;

        document.getElementById('mi-link').href = card.dataset.link || '#';
        $('#modal-intervento').modal('show');
    });

    function showAlert(type, html) {
        alertEl.className         = 'alert alert-' + type + ' py-2 mb-0';
        alertEl.innerHTML         = html;
        alertWrapEl.style.display = '';
    }
})();
</script>
<?= $this->endSection() ?>
