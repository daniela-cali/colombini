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
// Card pool (completa, con tecnici consigliati)
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
    ?>
    <?php
    $descr   = $i['descrizione'] ?? '';
    $luogo   = $i['luogo_intervento'] ?? '';
    $citta   = $i['citta'] ?? '';
    $luogoDisplay = $citta ?: $luogo;
    ?>
    <div class="intervention-card <?= esc($i['priorita']) ?>" data-id="<?= $i['id'] ?>"
         data-modal="1"
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

// Card zona tecnico (compatta, senza consigliati)
$renderZonaCard = function (array $i) use ($tipiPerCodice, $priorita): void {
    $tipoInfo = $tipiPerCodice[$i['tipo_intervento']] ?? ['id' => 0, 'nome' => $i['tipo_intervento'], 'icona' => 'fa-wrench'];
    $priInfo  = $priorita[$i['priorita']] ?? ['label' => $i['priorita'], 'badge' => 'badge-secondary'];
    $nomeCliente = '';
    if (!empty($i['cliente_tipo'])) {
        $nomeCliente = $i['cliente_tipo'] === 'persona_fisica'
            ? trim(($i['cliente_cognome'] ?? '') . ' ' . ($i['cliente_nome'] ?? ''))
            : ($i['ragsoc'] ?? '');
    }
    ?>
    <?php
    $descr = $i['descrizione'] ?? '';
    $luogo = $i['luogo_intervento'] ?? '';
    $citta = $i['citta'] ?? '';
    ?>
    <div class="intervention-card <?= esc($i['priorita']) ?>" data-id="<?= $i['id'] ?>"
         data-modal="1"
         data-mc="<?= htmlspecialchars($nomeCliente, ENT_QUOTES) ?>"
         data-mt="<?= htmlspecialchars($tipoInfo['nome'], ENT_QUOTES) ?>"
         data-mi="<?= htmlspecialchars($tipoInfo['icona'], ENT_QUOTES) ?>"
         data-mp="<?= htmlspecialchars($priInfo['label'], ENT_QUOTES) ?>"
         data-mb="<?= htmlspecialchars($priInfo['badge'], ENT_QUOTES) ?>"
         data-ml="<?= htmlspecialchars($luogo, ENT_QUOTES) ?>"
         data-mci="<?= htmlspecialchars($citta, ENT_QUOTES) ?>"
         data-md="<?= htmlspecialchars($descr, ENT_QUOTES) ?>"
         data-link="<?= base_url('interventi/' . $i['id']) ?>">
        <div class="d-flex align-items-center mb-1" style="gap:.35rem;">
            <span class="badge <?= esc($priInfo['badge']) ?>" style="font-size:.65rem;"><?= esc($priInfo['label']) ?></span>
            <small class="text-muted" title="<?= esc($tipoInfo['nome']) ?>">
                <i class="fas fa-<?= esc($tipoInfo['icona']) ?>"></i>
            </small>
        </div>
        <div class="font-weight-bold small mb-1 text-truncate">
            <?= $nomeCliente ? esc($nomeCliente) : '<em class="text-muted">—</em>' ?>
        </div>
    </div>
    <?php
};
?>

<!-- Toolbar -->
<div class="card card-outline card-primary mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt mr-1"></i> Pianificazione manuale
        </h3>
        <div class="card-tools d-flex align-items-center">
            <button id="btn-filtro" class="btn btn-outline-light btn-sm mr-3" style="display:none;"></button>
            <label class="mb-0 mr-2 small">Data viaggio:</label>
            <input type="date" id="data-viaggio" class="form-control form-control-sm mr-3"
                   value="<?= esc($data) ?>" style="width:155px;"
                   onchange="window.location.href='<?= base_url('pianificazione') ?>?data='+this.value">
            <button id="btn-salva" class="btn btn-success btn-sm" disabled>
                <i class="fas fa-save mr-1"></i> Salva bozze
            </button>
        </div>
    </div>
    <div class="card-body py-2">
        <span id="msg-stato" class="text-sm text-muted">
            <?php if (empty($interventi) && empty(array_filter($preAssegnati))): ?>
                Nessun intervento da pianificare.
            <?php else: ?>
                Trascina gli interventi nelle righe dei tecnici, poi salva le bozze.
            <?php endif; ?>
        </span>
        <div id="alert-salva" class="alert py-2 mb-0 mt-1" style="display:none;"></div>
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
                <div class="sortable-zone" id="pool-zone" data-tecnico="">
                    <?php if (empty($interventi)): ?>
                        <div class="pool-empty">
                            <i class="fas fa-check-circle fa-2x d-block mb-2 text-success" style="opacity:.5;"></i>
                            Tutti assegnati
                        </div>
                    <?php endif; ?>
                    <?php foreach ($interventi as $i): $renderPoolCard($i); endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Righe tecnici -->
    <div class="tecnici-panel">
        <?php
        // Mappa tipo_id → nome per elencare le competenze
        $tipiPerId = [];
        foreach ($tipiPerCodice as $info) {
            $tipiPerId[(int) $info['id']] = $info['nome'];
        }
        ?>
        <?php foreach ($tecnici as $t):
            $initials  = strtoupper(substr($t->nome, 0, 1) . substr($t->cognome, 0, 1));
            $colore    = $t->colore ?? '#6c757d';
            $preCards  = $preAssegnati[$t->id] ?? [];
            $rilevante = in_array($t->id, $tecniciRilevanti) ? '1' : '0';
            $bloccato  = isset($tecniciBloccati[$t->id]);
            $viaggioBloccatoId = $tecniciBloccati[$t->id] ?? null;

            $nomiComp = [];
            foreach ($competenzePerTecnico[$t->id] ?? [] as $tipoId => $livello) {
                if ($livello >= 2) {
                    $nomiComp[] = $tipiPerId[(int) $tipoId] ?? '?';
                }
            }
            $nComp = count($nomiComp);
        ?>
        <div class="card card-outline <?= $bloccato ? 'card-primary' : 'card-secondary' ?> mb-2 tech-row-wrap"
             data-rilevante="<?= $rilevante ?>">
            <div class="card-header py-2" style="background:<?= esc($colore) ?>; border-color:<?= esc($colore) ?>;">
                <div class="d-flex align-items-center">
                    <span class="tech-avatar mr-2"
                          style="background:rgba(0,0,0,.15); color:#212529;"><?= $initials ?></span>
                    <div style="flex:1; min-width:0;">
                        <div class="font-weight-bold small text-truncate" style="color:#212529;"><?= esc($t->cognome . ' ' . $t->nome) ?></div>
                        <small style="color:rgba(0,0,0,.55);">
                            <?= $nComp ?> competenz<?= $nComp === 1 ? 'a' : 'e' ?>
                            <?php if (!empty($nomiComp)): ?>
                                : <?= esc(implode(', ', $nomiComp)) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php if ($bloccato): ?>
                        <a href="<?= base_url('viaggi/' . $viaggioBloccatoId) ?>"
                           class="badge ml-2 text-white"
                           style="background:rgba(0,0,0,.25);"
                           title="Viaggio approvato — clicca per aprirlo e riapri in bozza">
                            <i class="fas fa-lock mr-1"></i> Approvato
                        </a>
                    <?php else: ?>
                        <span class="badge ml-2" id="count-<?= $t->id ?>"
                              style="background:rgba(0,0,0,.15); color:#212529;">0</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if ($bloccato): ?>
                    <div class="text-center py-3 text-muted small">
                        <i class="fas fa-lock mr-1"></i>
                        Viaggio approvato.
                        <a href="<?= base_url('viaggi/' . $viaggioBloccatoId) ?>">Apri il viaggio</a>
                        e usa <strong>Riapri</strong> per modificarlo.
                    </div>
                <?php else: ?>
                <div class="sortable-zone tech-drop-zone" id="zone-<?= $t->id ?>" data-tecnico="<?= $t->id ?>">
                    <div class="drop-empty" id="empty-<?= $t->id ?>"
                         <?= !empty($preCards) ? 'style="display:none;"' : '' ?>>
                        <i class="fas fa-arrow-left" style="opacity:.35;"></i> Trascina qui
                    </div>
                    <?php foreach ($preCards as $i): $renderZonaCard($i); endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Modal dettaglio intervento -->
<div class="modal fade" id="modal-intervento" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span id="mi-badge" class="badge mr-2"></span>
                    <i id="mi-icona" class="fas fa-wrench mr-1"></i>
                    <span id="mi-tipo"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <dl class="mb-0">
                    <dt class="small text-muted">Cliente</dt>
                    <dd id="mi-cliente" class="mb-2 font-weight-bold"></dd>

                    <dt class="small text-muted" id="mi-luogo-label" style="display:none;">Luogo</dt>
                    <dd id="mi-luogo" class="mb-2"></dd>

                    <dt class="small text-muted" id="mi-descr-label" style="display:none;">Descrizione</dt>
                    <dd id="mi-descr" class="mb-0 text-sm"></dd>
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

<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/pianificazione/index') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var btnSalva  = document.getElementById('btn-salva');
    var msgEl     = document.getElementById('msg-stato');
    var alertEl   = document.getElementById('alert-salva');
    var btnFiltro = document.getElementById('btn-filtro');

    // --- Filtro tecnici per competenza ---
    var filtroAttivo = <?= (!empty($tecniciRilevanti) && count($tecniciRilevanti) < count($tecnici)) ? 'true' : 'false' ?>;

    function applyFiltro() {
        var nascosti = 0;
        document.querySelectorAll('.tech-row-wrap[data-rilevante]').forEach(function (row) {
            var rilevante = row.dataset.rilevante === '1';
            var haCards   = row.querySelectorAll('.intervention-card').length > 0;
            var visible   = !filtroAttivo || rilevante || haCards;
            row.style.display = visible ? '' : 'none';
            if (!visible) nascosti++;
        });

        if (filtroAttivo && nascosti > 0) {
            btnFiltro.innerHTML = '<i class="fas fa-users mr-1"></i> Mostra tutti <span class="badge badge-light ml-1">' + nascosti + '</span>';
            btnFiltro.style.display = '';
        } else if (!filtroAttivo) {
            btnFiltro.innerHTML = '<i class="fas fa-filter mr-1"></i> Filtra per competenza';
            btnFiltro.style.display = '';
        } else {
            btnFiltro.style.display = 'none';
        }
    }

    btnFiltro.addEventListener('click', function () {
        filtroAttivo = !filtroAttivo;
        applyFiltro();
    });

    // --- SortableJS ---
    document.querySelectorAll('.sortable-zone').forEach(function (el) {
        Sortable.create(el, {
            group:      'board',
            animation:  150,
            ghostClass: 'sortable-ghost',
            onEnd:      function () { updateUI(); updateStepNumbers(); },
        });
    });

    // Aggiorna numerazione tappe nelle zone tecnico
    function updateStepNumbers() {
        document.querySelectorAll('.tech-drop-zone').forEach(function (zone) {
            zone.querySelectorAll('.intervention-card').forEach(function (card, idx) {
                var span = card.querySelector('.step-num');
                if (!span) {
                    span = document.createElement('span');
                    span.className = 'step-num';
                    card.insertBefore(span, card.firstChild);
                }
                span.textContent = idx + 1;
            });
        });
        // Rimuove il numero se la card torna nel pool
        document.querySelectorAll('#pool-zone .step-num').forEach(function (s) { s.remove(); });
    }

    function updateUI() {
        var assigned = 0;

        document.querySelectorAll('.sortable-zone[data-tecnico]').forEach(function (zone) {
            var tecnicoId = zone.dataset.tecnico;
            if (!tecnicoId) return;

            var count = zone.querySelectorAll('.intervention-card').length;
            var empty = document.getElementById('empty-' + tecnicoId);
            if (empty) empty.style.display = count > 0 ? 'none' : '';

            var badge = document.getElementById('count-' + tecnicoId);
            if (badge) badge.textContent = count;

            assigned += count;
        });

        var pool = document.querySelectorAll('#pool-zone .intervention-card').length;
        document.getElementById('pool-count').textContent = pool;

        if (assigned > 0) {
            msgEl.textContent = assigned + (assigned === 1 ? ' intervento assegnato.' : ' interventi assegnati.');
            btnSalva.disabled = false;
        } else {
            msgEl.textContent = 'Trascina gli interventi nelle righe dei tecnici, poi salva le bozze.';
            btnSalva.disabled = true;
        }
        alertEl.style.display = 'none';
    }

    updateUI();
    updateStepNumbers();
    applyFiltro();

    // Messaggio di successo post-save
    (function () {
        var params = new URLSearchParams(window.location.search);
        var saved  = params.get('saved');
        if (saved) {
            showAlert('success', '<i class="fas fa-check-circle mr-1"></i> ' + saved
                + ' — <a href="<?= base_url('viaggi') ?>">Vai ai Viaggi</a> per autorizzarli.');
            history.replaceState(null, '', window.location.pathname + '?data=' + params.get('data'));
        }
    })();

    btnSalva.addEventListener('click', function () {
        var data         = document.getElementById('data-viaggio').value;
        var assegnazioni = [];

        document.querySelectorAll('.sortable-zone[data-tecnico]').forEach(function (zone) {
            var tecnicoId = zone.dataset.tecnico;
            if (!tecnicoId) return;

            var tappe = [];
            zone.querySelectorAll('.intervention-card[data-id]').forEach(function (card) {
                tappe.push({ intervento_id: parseInt(card.dataset.id) });
            });
            if (tappe.length > 0) {
                assegnazioni.push({ tecnico_id: parseInt(tecnicoId), tappe: tappe });
            }
        });

        if (assegnazioni.length === 0) {
            showAlert('warning', '<i class="fas fa-exclamation-triangle mr-1"></i> Nessun intervento assegnato.');
            return;
        }

        btnSalva.disabled = true;
        btnSalva.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Salvataggio…';

        fetch('<?= base_url('pianificazione/salva') ?>', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body:    JSON.stringify({ data: data, assegnazioni: assegnazioni }),
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            if (json.ok) {
                window.location.href = '<?= base_url('pianificazione') ?>?data=' + data
                    + '&saved=' + encodeURIComponent(json.msg);
            } else {
                showAlert('danger', '<i class="fas fa-times-circle mr-1"></i> ' + (json.errore || 'Errore.'));
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="fas fa-save mr-1"></i> Salva bozze';
            }
        })
        .catch(function () {
            showAlert('danger', '<i class="fas fa-times-circle mr-1"></i> Errore di rete.');
            btnSalva.disabled = false;
            btnSalva.innerHTML = '<i class="fas fa-save mr-1"></i> Salva bozze';
        });
    });

    // --- Modal dettaglio intervento ---
    var dragging = false;
    document.querySelectorAll('.sortable-zone').forEach(function (el) {
        el.addEventListener('sortstart', function () { dragging = true; });
        el.addEventListener('sortend',   function () { setTimeout(function () { dragging = false; }, 50); });
    });

    document.addEventListener('click', function (e) {
        if (dragging) return;
        var card = e.target.closest('.intervention-card[data-modal]');
        if (!card) return;

        var luogo  = [card.dataset.ml, card.dataset.mci].filter(Boolean).join(' — ');
        var descr  = card.dataset.md || '';

        document.getElementById('mi-badge').className  = 'badge mr-2 ' + (card.dataset.mb || 'badge-secondary');
        document.getElementById('mi-badge').textContent = card.dataset.mp || '';
        document.getElementById('mi-icona').className  = 'fas ' + (card.dataset.mi || 'fa-wrench') + ' mr-1';
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
    // --- Fine modal ---

    function showAlert(type, html) {
        alertEl.className = 'alert alert-' + type + ' py-2 mb-0 mt-1';
        alertEl.innerHTML = html;
        alertEl.style.display = '';
    }
})();
</script>
<?= $this->endSection() ?>
