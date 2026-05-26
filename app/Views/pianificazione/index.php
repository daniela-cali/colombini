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
// Closure: card nel pool (piena, con tecnici consigliati)
$renderPoolCard = function (array $i) use ($tipiPerCodice, $priorita, $tecnici, $competenzePerTecnico): void {
    $tipoInfo = $tipiPerCodice[$i['tipo_intervento']] ?? ['id' => 0, 'nome' => $i['tipo_intervento'], 'icona' => 'fa-wrench'];
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
    <div class="intervention-card <?= esc($i['priorita']) ?>" data-id="<?= $i['id'] ?>"
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

// Closure: card nella giornata (già pianificata, con ora + badge tecnico + X)
$renderGiornataCard = function (array $i) use ($tipiPerCodice, $priorita): void {
    $tipoInfo = $tipiPerCodice[$i['tipo_intervento']] ?? ['id' => 0, 'nome' => $i['tipo_intervento'], 'icona' => 'fa-wrench'];
    $priInfo  = $priorita[$i['priorita']] ?? ['label' => $i['priorita'], 'badge' => 'badge-secondary'];
    $nomeCliente = '';
    if (!empty($i['cliente_tipo'])) {
        $nomeCliente = $i['cliente_tipo'] === 'persona_fisica'
            ? trim(($i['cliente_cognome'] ?? '') . ' ' . ($i['cliente_nome'] ?? ''))
            : ($i['ragsoc'] ?? '');
    }
    $descr     = $i['descrizione'] ?? '';
    $luogo     = $i['luogo_intervento'] ?? '';
    $citta     = $i['citta'] ?? '';
    $ora       = $i['data_pianificata'] ? date('H:i', strtotime($i['data_pianificata'])) : '';
    $tecNome   = trim(($i['tecnico_cognome'] ?? '') . ' ' . ($i['tecnico_nome'] ?? ''));
    $tecColore = $i['tecnico_colore'] ?? '#6c757d';
    $tecIni    = $tecNome
        ? strtoupper(substr($i['tecnico_nome'] ?? '', 0, 1) . substr($i['tecnico_cognome'] ?? '', 0, 1))
        : '';
    $canRevert = ($i['stato'] === 'pianificato');
    ?>
    <div class="intervention-card <?= esc($i['priorita']) ?>" data-id="<?= $i['id'] ?>"
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
        <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="d-flex align-items-center" style="gap:.3rem;">
                <span class="badge <?= esc($priInfo['badge']) ?>" style="font-size:.65rem;"><?= esc($priInfo['label']) ?></span>
                <small class="text-muted" title="<?= esc($tipoInfo['nome']) ?>">
                    <i class="fas <?= esc($tipoInfo['icona']) ?>"></i>
                </small>
            </div>
            <div class="d-flex align-items-center giornata-header-tools" style="gap:.3rem;">
                <?php if ($ora): ?>
                    <small class="text-muted giornata-ora"><?= esc($ora) ?></small>
                <?php endif; ?>
                <?php if ($canRevert): ?>
                    <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-rimuovi-giornata"
                            title="Rimuovi dalla pianificazione" style="line-height:1;">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="font-weight-bold small mb-1 text-truncate">
            <?= $nomeCliente ? esc($nomeCliente) : '<em class="text-muted">Senza cliente</em>' ?>
        </div>
        <?php if ($citta || $luogo): ?>
        <div class="small text-muted mb-1">
            <i class="fas fa-map-marker-alt mr-1"></i><?= esc($citta ?: $luogo) ?>
        </div>
        <?php endif; ?>
        <?php if ($tecNome): ?>
        <div class="d-flex align-items-center mt-1 giornata-tech-row" style="gap:.25rem;">
            <span class="tech-dot" style="background:<?= esc($tecColore) ?>"><?= esc($tecIni) ?></span>
            <small class="text-muted"><?= esc(trim($i['tecnico_cognome'] ?? '')) ?></small>
        </div>
        <?php endif; ?>
    </div>
    <?php
};
?>

<!-- Toolbar -->
<div class="card card-outline card-primary mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt mr-1"></i> Pianificazione
        </h3>
        <div class="card-tools d-flex align-items-center">
            <label class="mb-0 mr-2 small text-muted">Data:</label>
            <input type="date" id="data-viaggio" class="form-control form-control-sm" style="width:155px;"
                   value="<?= esc($data) ?>"
                   onchange="window.location.href='<?= base_url('pianificazione') ?>?data='+this.value">
        </div>
    </div>
    <div class="card-body py-2">
        <!-- Barra settimanale -->
        <?php $giorniBrevi = ['Lun','Mar','Mer','Gio','Ven','Sab','Dom']; ?>
        <div class="d-flex mb-2" style="gap:4px;">
            <?php foreach ($settimana as $idx => $g):
                $isOggi        = $g['data'] === date('Y-m-d');
                $isSelezionato = $g['data'] === $data;
                $isWeekend     = $idx >= 5;
                $btnClass      = $isSelezionato ? 'btn-primary' : ($isOggi ? 'btn-outline-primary' : 'btn-outline-secondary');
            ?>
            <a href="<?= base_url('pianificazione?data=' . $g['data']) ?>"
               class="btn btn-sm <?= $btnClass ?> flex-fill text-center p-1<?= $isWeekend ? ' opacity-50' : '' ?>"
               style="min-width:0;<?= $isWeekend ? 'opacity:.5;' : '' ?>">
                <div style="font-size:.65rem;"><?= $giorniBrevi[$idx] ?></div>
                <div style="font-size:.7rem;"><?= date('d/m', strtotime($g['data'])) ?></div>
                <?php if ($g['count'] > 0): ?>
                    <span class="badge badge-<?= $isSelezionato ? 'light' : 'primary' ?> mt-1"
                          style="font-size:.6rem;"><?= $g['count'] ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <div id="alert-salva" class="alert py-2 mb-0" style="display:none;"></div>
    </div>
</div>

<!-- Layout pianificazione -->
<div class="planning-layout">

    <!-- Pool sidebar -->
    <div class="pool-panel">
        <div class="card card-outline card-primary">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fas fa-inbox mr-1 text-primary"></i> Da pianificare
                    <span class="badge badge-primary ml-1" id="pool-count"><?= count($interventi) ?></span>
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
                            <span class="badge badge-secondary pool-tipo-count"><?= count($invPerTipo) ?></span>
                        </a>
                        <div class="collapse show sortable-zone pool-zone" id="<?= $collapseId ?>"
                             data-tipo="<?= esc($tipo) ?>">
                            <?php foreach ($invPerTipo as $i): $renderPoolCard($i); endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Giornata -->
    <div class="giornata-panel">
        <div class="card card-outline card-success">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fas fa-calendar-day mr-1 text-success"></i>
                    Giornata del <?= date('d/m/Y', strtotime($data)) ?>
                    <span class="badge badge-success ml-1" id="giornata-count"><?= count($giornataInterventi) ?></span>
                </h3>
                <div class="card-tools">
                    <small class="text-muted d-none d-md-inline">
                        <i class="fas fa-arrow-left mr-1"></i>Trascina gli interventi qui
                    </small>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="sortable-zone giornata-drop-zone" id="giornata-zone">
                    <?php if (empty($giornataInterventi)): ?>
                    <div id="giornata-empty" class="giornata-empty">
                        <i class="fas fa-calendar-day fa-2x mb-2" style="opacity:.2;"></i>
                        <span>Nessun intervento pianificato per questa giornata</span>
                    </div>
                    <?php else: ?>
                    <div id="giornata-empty" class="drop-empty" style="display:none;"></div>
                    <?php foreach ($giornataInterventi as $i): $renderGiornataCard($i); endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal dettaglio intervento (click su card) -->
<div class="modal fade" id="modal-intervento" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title d-flex align-items-center gap-2">
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
                    <dd id="mi-descr" class="mb-0 text-sm" style="display:none;"></dd>
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

<!-- Modal assegna tecnico (al drag nella giornata) -->
<div class="modal fade" id="modal-assegna" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success py-2">
                <h5 class="modal-title text-white">
                    <i class="fas fa-calendar-plus mr-1"></i> Pianifica intervento
                </h5>
            </div>
            <div class="modal-body">
                <p class="mb-0 font-weight-bold" id="assegna-cliente"></p>
                <p class="small text-muted mb-3" id="assegna-tipo"></p>
                <div class="form-group">
                    <label class="small">Data e ora <span class="text-danger">*</span></label>
                    <input type="datetime-local" id="assegna-data" class="form-control form-control-sm" required>
                    <small id="assegna-orario-note" class="text-muted" style="display:none;"></small>
                </div>
                <div class="form-group mb-0">
                    <label class="small">Tecnico</label>
                    <select id="assegna-tecnico" class="form-control form-control-sm">
                        <option value="">— Non assegnato —</option>
                        <?php foreach ($tecnici as $t): ?>
                            <option value="<?= $t->id ?>"><?= esc($t->cognome . ' ' . $t->nome) ?></option>
                        <?php endforeach; ?>
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var alertEl   = document.getElementById('alert-salva');

    var pendingCard   = null;
    var pendingSource = null;
    var dragging      = false;

    var giornataEl = document.getElementById('giornata-zone');

    // Pool zones
    document.querySelectorAll('.pool-zone').forEach(function (el) {
        Sortable.create(el, {
            group:      { name: 'board', pull: true, put: true },
            animation:  150,
            ghostClass: 'sortable-ghost',
            onStart:    function () { dragging = true; },
            onEnd:      function () { setTimeout(function () { dragging = false; }, 100); },
        });
    });

    // Giornata zone: riceve card dal pool, non le restituisce via drag
    Sortable.create(giornataEl, {
        group:      { name: 'board', pull: false, put: true },
        animation:  150,
        ghostClass: 'sortable-ghost',
        onStart:    function () { dragging = true; },
        onEnd:      function () { setTimeout(function () { dragging = false; }, 100); },
        onAdd:      function (evt) {
            var card = evt.item;
            if (card.dataset.saved === '1') return;
            pendingCard   = card;
            pendingSource = evt.from;
            showModalAssegna(card);
        },
    });

    // --- Modal assegna ---
    var $modalAssegna    = $('#modal-assegna');
    var assegnaDataEl    = document.getElementById('assegna-data');
    var assegnaTecnicoEl = document.getElementById('assegna-tecnico');
    var assegnaSuggEl    = document.getElementById('assegna-suggerimento');
    var assegnaOraNote   = document.getElementById('assegna-orario-note');

    function aggiornaOrarioSuggerito(tecnicoId) {
        var dataViaggio = document.getElementById('data-viaggio').value;
        fetch('<?= base_url('interventi/api/orario-suggerito') ?>?tecnico_id='
              + encodeURIComponent(tecnicoId || 0) + '&data=' + encodeURIComponent(dataViaggio))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                assegnaDataEl.value = dataViaggio + 'T' + data.ora;
                if (data.n_prev > 0) {
                    assegnaOraNote.textContent = 'Stimato dopo ' + data.n_prev
                        + ' intervento' + (data.n_prev > 1 ? 'i' : '') + ' già pianificato'
                        + (data.n_prev > 1 ? 'i' : '') + ' (' + data.ora + ')';
                    assegnaOraNote.style.display = '';
                } else {
                    assegnaOraNote.style.display = 'none';
                }
            });
    }

    assegnaTecnicoEl.addEventListener('change', function () {
        aggiornaOrarioSuggerito(this.value || 0);
    });

    function showModalAssegna(card) {
        var dataViaggio = document.getElementById('data-viaggio').value;
        assegnaDataEl.value     = dataViaggio + 'T08:00';
        assegnaTecnicoEl.value  = '';
        assegnaSuggEl.innerHTML = '';
        assegnaOraNote.style.display = 'none';
        assegnaDataEl.classList.remove('is-invalid');

        document.getElementById('assegna-cliente').textContent = card.dataset.mc || '—';
        document.getElementById('assegna-tipo').textContent    = card.dataset.mt || '';

        // Orario suggerito senza tecnico (usa ora inizio generica)
        aggiornaOrarioSuggerito(0);

        var tipo      = card.dataset.tc  || '';
        var clienteId = card.dataset.cid || 0;
        if (tipo) {
            fetch('<?= base_url('interventi/api/tecnico-consigliato') ?>?tipo_intervento='
                  + encodeURIComponent(tipo) + '&cliente_id=' + encodeURIComponent(clienteId))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.tecnico) return;
                    var t = data.tecnico;
                    assegnaSuggEl.innerHTML =
                        '<div class="alert alert-info py-1 px-2 mb-0 small">' +
                        '<i class="fas fa-lightbulb mr-1"></i>Consigliato: <strong>' +
                        t.cognome + ' ' + t.nome + '</strong>' +
                        ' <span class="text-muted">(' + t.cnt + ' int. simili)</span>' +
                        ' <a href="#" class="ml-2" onclick="var s=document.getElementById(\'assegna-tecnico\');' +
                        's.value=\'' + t.tecnico_id + '\';s.dispatchEvent(new Event(\'change\'));' +
                        'this.parentElement.remove();return false;">Usa questo</a>' +
                        '</div>';
                });
        }

        $modalAssegna.modal('show');
    }

    // Chiusura modal (qualsiasi modo) → ripristina card nel pool
    $modalAssegna.on('hide.bs.modal', function () {
        if (pendingCard && pendingSource) {
            pendingSource.appendChild(pendingCard);
            pendingCard   = null;
            pendingSource = null;
            updateUI();
        }
    });

    document.getElementById('btn-assegna-annulla').addEventListener('click', function () {
        $modalAssegna.modal('hide');
    });

    // Conferma pianifica → AJAX
    document.getElementById('btn-assegna-conferma').addEventListener('click', function () {
        var card = pendingCard;
        if (!card) return;

        var dataPianificata = assegnaDataEl.value;
        if (!dataPianificata) {
            assegnaDataEl.classList.add('is-invalid');
            return;
        }
        assegnaDataEl.classList.remove('is-invalid');

        var tecnicoId = assegnaTecnicoEl.value || '';
        var btnConf   = this;
        btnConf.disabled  = true;
        btnConf.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Salvo…';

        var formData = new FormData();
        formData.append('data_pianificata', dataPianificata);
        if (tecnicoId) formData.append('tecnico_id', tecnicoId);

        fetch('<?= base_url('interventi/') ?>' + card.dataset.id + '/pianifica', {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body:    formData,
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            btnConf.disabled  = false;
            btnConf.innerHTML = '<i class="fas fa-check mr-1"></i> Pianifica';
            if (json.ok) {
                csrfToken  = json.csrf || csrfToken;
                card.dataset.saved = '1';
                decoraCardGiornata(card, tecnicoId, dataPianificata);
                pendingCard   = null;
                pendingSource = null;
                $modalAssegna.modal('hide');
                updateUI();
            } else {
                showAlert('danger', '<i class="fas fa-times-circle mr-1"></i>' + (json.msg || 'Errore durante il salvataggio.'));
            }
        })
        .catch(function () {
            btnConf.disabled  = false;
            btnConf.innerHTML = '<i class="fas fa-check mr-1"></i> Pianifica';
            showAlert('danger', '<i class="fas fa-times-circle mr-1"></i> Errore di rete.');
        });
    });

    // Mappa tecnico_id → dati per badge
    var tecniciMap = {
        <?php foreach ($tecnici as $t): ?>
        '<?= $t->id ?>': { nome: '<?= addslashes(esc($t->nome)) ?>', cognome: '<?= addslashes(esc($t->cognome)) ?>', colore: '<?= addslashes(esc($t->colore ?? '#6c757d')) ?>' },
        <?php endforeach; ?>
    };

    function decoraCardGiornata(card, tecnicoId, dataPianificata) {
        var ora = dataPianificata ? dataPianificata.split('T')[1].substring(0, 5) : '';

        // Header-tools: aggiungi ora + bottone X
        var headerTools = card.querySelector('.giornata-header-tools');
        if (!headerTools) {
            headerTools = document.createElement('div');
            headerTools.className = 'd-flex align-items-center giornata-header-tools';
            headerTools.style.gap = '.3rem';
            var firstRow = card.querySelector('.d-flex');
            if (firstRow) firstRow.appendChild(headerTools);
        }

        if (ora && !headerTools.querySelector('.giornata-ora')) {
            var oraEl = document.createElement('small');
            oraEl.className   = 'text-muted giornata-ora';
            oraEl.textContent = ora;
            headerTools.appendChild(oraEl);
        }

        if (!headerTools.querySelector('.btn-rimuovi-giornata')) {
            var btnX = document.createElement('button');
            btnX.type      = 'button';
            btnX.className = 'btn btn-xs btn-link text-danger p-0 btn-rimuovi-giornata';
            btnX.title     = 'Rimuovi dalla pianificazione';
            btnX.style.lineHeight = '1';
            btnX.innerHTML = '<i class="fas fa-times"></i>';
            btnX.addEventListener('click', rimuoviCardHandler);
            headerTools.appendChild(btnX);
        }

        // Badge tecnico
        var techRow = card.querySelector('.giornata-tech-row');
        if (tecnicoId && tecniciMap[tecnicoId]) {
            var t   = tecniciMap[tecnicoId];
            var ini = (t.nome.charAt(0) + t.cognome.charAt(0)).toUpperCase();
            var html = '<div class="d-flex align-items-center mt-1 giornata-tech-row" style="gap:.25rem;">' +
                '<span class="tech-dot" style="background:' + t.colore + '">' + ini + '</span>' +
                '<small class="text-muted">' + t.cognome + '</small></div>';
            if (techRow) {
                techRow.outerHTML = html;
            } else {
                card.insertAdjacentHTML('beforeend', html);
            }
        } else if (techRow) {
            techRow.remove();
        }
    }

    // Rimuovi card dalla giornata (X button) → reload pagina
    function rimuoviCardHandler(e) {
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
                showAlert('danger', json.msg || 'Errore durante la rimozione.');
            }
        })
        .catch(function () {
            card.style.opacity = '';
            showAlert('danger', 'Errore di rete.');
        });
    }

    // Attacca handler X ai bottoni pre-esistenti (card già pianificate dal server)
    document.querySelectorAll('.btn-rimuovi-giornata').forEach(function (btn) {
        btn.addEventListener('click', rimuoviCardHandler);
    });

    // --- Aggiorna contatori ---
    function updateUI() {
        var pool     = document.querySelectorAll('.pool-zone .intervention-card').length;
        var giornata = document.querySelectorAll('#giornata-zone .intervention-card').length;

        document.getElementById('pool-count').textContent     = pool;
        document.getElementById('giornata-count').textContent = giornata;

        var emptyEl = document.getElementById('giornata-empty');
        if (emptyEl) emptyEl.style.display = giornata > 0 ? 'none' : '';

        // Aggiorna badge conteggio per gruppo tipo nel pool
        document.querySelectorAll('.pool-zone').forEach(function (zone) {
            var cnt   = zone.querySelectorAll('.intervention-card').length;
            var badge = zone.previousElementSibling
                ? zone.previousElementSibling.querySelector('.pool-tipo-count')
                : null;
            if (badge) badge.textContent = cnt;
        });
    }

    updateUI();

    // --- Modal dettaglio intervento (click su card) ---
    document.addEventListener('click', function (e) {
        if (dragging) return;
        if (e.target.closest('.btn-rimuovi-giornata')) return;
        var card = e.target.closest('.intervention-card[data-modal]');
        if (!card) return;

        var luogo = [card.dataset.ml, card.dataset.mci].filter(Boolean).join(' — ');
        var descr = card.dataset.md || '';

        document.getElementById('mi-badge').className    = 'badge mr-2 ' + (card.dataset.mb || 'badge-secondary');
        document.getElementById('mi-badge').textContent  = card.dataset.mp || '';
        document.getElementById('mi-icona').className   = 'fas ' + (card.dataset.mi || 'fa-wrench') + ' mr-1';
        document.getElementById('mi-tipo').textContent   = card.dataset.mt || '';
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
        alertEl.className     = 'alert alert-' + type + ' py-2 mb-0';
        alertEl.innerHTML     = html;
        alertEl.style.display = '';
    }
})();
</script>
<?= $this->endSection() ?>
