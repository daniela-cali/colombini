<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Calendario</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/calendario.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$prioritaInfo = [
    'urgente'     => ['label' => 'Urgente',     'badge' => 'badge-danger'],
    'ordinario'   => ['label' => 'Ordinario',   'badge' => 'badge-warning'],
    'programmato' => ['label' => 'Programmato', 'badge' => 'badge-success'],
];
?>

<div id="cal-layout">

    <!-- Pool sidebar -->
    <div id="pool-panel" class="mb-3">
        <div class="card card-primary">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fas fa-inbox mr-1 text-primary"></i>
                    Da pianificare
                    <span class="badge badge-primary ml-1" id="pool-count"><?= count($pool) ?></span>
                </h3>
            </div>
            <div class="card-body p-2">
                <p class="small text-muted mb-2 px-1">
                    <i class="fas fa-arrows-alt mr-1"></i>Trascina una card sul calendario
                </p>
                <div id="pool-container">
                    <?php if (empty($pool)): ?>
                    <div class="pool-empty">
                        <i class="fas fa-check-circle fa-2x d-block mb-2 text-success" style="opacity:.5;"></i>
                        Tutti pianificati
                    </div>
                    <?php else: ?>
                    <?php foreach ($poolPerTipo as $tipo => $invPerTipo):
                        $tipoInfo   = $tipiPerCodice[$tipo] ?? ['id' => 0, 'nome' => $tipo, 'icona' => 'fa-wrench', 'durata_default' => 60];
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
                        <div class="collapse pool-zone" id="<?= $collapseId ?>">
                        <?php foreach ($invPerTipo as $i):
                            $priInfo     = $prioritaInfo[$i['priorita']] ?? ['label' => $i['priorita'], 'badge' => 'badge-secondary'];
                            $nomeCliente = '';
                            if (!empty($i['cliente_tipo'])) {
                                $nomeCliente = $i['cliente_tipo'] === 'persona_fisica'
                                    ? trim(($i['cliente_cognome'] ?? '') . ' ' . ($i['cliente_nome'] ?? ''))
                                    : ($i['ragsoc'] ?? '');
                            }
                            $adatti = [];
                            foreach ($tecnici as $t) {
                                if (($competenzePerTecnico[$t['id']][$tipoInfo['id']] ?? 0) >= 2) {
                                    $adatti[] = $t;
                                }
                            }
                        ?>
                        <div class="pool-card <?= esc($i['priorita']) ?>"
                             data-id="<?= $i['id'] ?>"
                             data-tipo="<?= esc($i['tipo_intervento']) ?>"
                             data-cliente="<?= htmlspecialchars($nomeCliente, ENT_QUOTES) ?>"
                             data-cliente-id="<?= (int)($i['cliente_id'] ?? 0) ?>"
                             data-durata="<?= (int)$tipoInfo['durata_default'] ?>"
                             data-tipo-nome="<?= htmlspecialchars($tipoInfo['nome'], ENT_QUOTES) ?>"
                             data-icona="<?= htmlspecialchars($tipoInfo['icona'], ENT_QUOTES) ?>"
                             data-luogo="<?= htmlspecialchars($i['luogo_intervento'] ?? '', ENT_QUOTES) ?>"
                             data-citta="<?= htmlspecialchars($i['cliente_citta'] ?? '', ENT_QUOTES) ?>"
                             data-descr="<?= htmlspecialchars($i['descrizione'] ?? '', ENT_QUOTES) ?>">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center" style="gap:.3rem;">
                                    <span class="badge <?= esc($priInfo['badge']) ?>" style="font-size:.65rem;"><?= esc($priInfo['label']) ?></span>
                                </div>
                                <small class="text-muted">
                                    <?php if (!empty($i['data_entro'])): ?>
                                        <i class="fas fa-clock" style="font-size:.65rem;"></i> <?= date('d/m', strtotime($i['data_entro'])) ?> ·
                                    <?php endif; ?>
                                    #<?= $i['id'] ?>
                                    <?php if (isset($i['distanza_km'])): ?>
                                        · <?= number_format($i['distanza_km'], 1) ?> km
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="font-weight-bold small mb-1 text-truncate">
                                <?= $nomeCliente ? esc($nomeCliente) : '<em class="text-muted">Senza cliente</em>' ?>
                            </div>
                            <?php
                                $luogoBadge = $i['cliente_citta'] ?: $i['luogo_intervento'] ?? '';
                            ?>
                            <?php if ($luogoBadge): ?>
                            <div class="small text-muted mb-1 text-truncate">
                                <i class="fas fa-map-marker-alt mr-1"></i><?= esc($luogoBadge) ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($i['descrizione'])): ?>
                            <div class="small text-muted text-truncate" style="font-size:.74rem;">
                                <?= esc(mb_strimwidth($i['descrizione'], 0, 55, '…')) ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($adatti)): ?>
                            <div class="mt-1 d-flex align-items-center flex-wrap" style="gap:.2rem;">
                                <small class="text-muted" style="font-size:.68rem;">Cons.:</small>
                                <?php foreach ($adatti as $at):
                                    $ini = strtoupper(substr($at['nome'], 0, 1) . substr($at['cognome'], 0, 1));
                                ?>
                                <span class="tech-dot" style="background:<?= esc($at['colore'] ?? '#6c757d') ?>"
                                      title="<?= esc($at['cognome'] . ' ' . $at['nome']) ?>"><?= esc($ini) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="resize-handle" title="Trascina per ridimensionare"></div>

    <!-- Calendar column -->
    <div id="cal-column">

        <!-- Filter buttons + generate trip form -->
        <div class="mb-2 d-flex flex-wrap justify-content-between align-items-center" style="gap:.4rem;">
            <div class="d-flex flex-wrap" style="gap:.4rem;">
                <button type="button" class="btn btn-sm btn-secondary btn-filtro active" data-id="">
                    <i class="fas fa-users mr-1"></i>Tutti
                </button>
                <?php foreach ($tecnici as $t): ?>
                <button type="button" class="btn btn-sm btn-filtro"
                        data-id="<?= $t['id'] ?>"
                        data-colore="<?= esc($t['colore'] ?: '#6c757d') ?>"
                        style="background:<?= esc($t['colore'] ?: '#6c757d') ?>;border-color:<?= esc($t['colore'] ?: '#6c757d') ?>;color:#fff;">
                    <?= esc($t['cognome'] . ' ' . $t['nome']) ?>
                </button>
                <?php endforeach; ?>
            </div>

            <form method="post" action="<?= base_url('calendario/genera-viaggio-giornata') ?>"
                  class="d-flex align-items-center" style="gap:.5rem;"
                  target="<?= strpos($_SERVER['HTTP_USER_AGENT'] ?? '', 'Mobile') === false ? '_blank' : '_self' ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="tecnico_id" id="form-tecnico-giornata" value="">
                <input type="hidden" name="data"       id="form-data-giornata"    value="">
                <small id="label-data-giornata" class="text-muted">Clicca un giorno sul calendario</small>
                <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap" id="btn-genera-viaggio" disabled>
                    <i class="fas fa-route mr-1"></i>Genera viaggio
                </button>
            </form>
        </div>

        <?php if (!empty($scadenzeSettimana)): ?>
        <div class="alert alert-warning py-2 mb-2 d-flex align-items-center flex-wrap" style="gap:.5rem;">
            <small class="font-weight-bold text-nowrap">
                <i class="fas fa-clock mr-1"></i>Scadenze aperte:
            </small>
            <?php foreach ($scadenzeSettimana as $s):
                $cn = $s['ragsoc'] ?: trim(($s['cliente_cognome'] ?? '') . ' ' . ($s['cliente_nome'] ?? '')) ?: '—';
            ?>
            <a href="<?= base_url('interventi/' . $s['id']) ?>"
               class="badge badge-light border font-weight-normal text-dark">
                <?= esc($cn) ?> <span class="text-muted">· <?= date('d/m', strtotime($s['data_entro'])) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-2 p-md-3">
                <div id="calendario"></div>
            </div>
        </div>

    </div>
</div>

<!-- Modal dettaglio intervento (click su evento FC) -->
<div class="modal fade" id="modalIntervento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-tools mr-2" id="modal-icona"></i>
                    <span id="modal-cliente"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted font-weight-normal small">Tipo</dt>
                    <dd class="col-sm-8" id="modal-tipo"></dd>
                    <dt class="col-sm-4 text-muted font-weight-normal small">Tecnico</dt>
                    <dd class="col-sm-8" id="modal-tecnico"></dd>
                    <dt class="col-sm-4 text-muted font-weight-normal small">Data</dt>
                    <dd class="col-sm-8" id="modal-data"></dd>
                    <dt class="col-sm-4 text-muted font-weight-normal small">Stato</dt>
                    <dd class="col-sm-8" id="modal-stato"></dd>
                    <dt class="col-sm-4 text-muted font-weight-normal small" id="modal-entro-label" style="display:none;">Entro il</dt>
                    <dd class="col-sm-8" id="modal-entro" style="display:none;"></dd>
                </dl>
                <div id="modal-descrizione-wrap" class="mt-3 pt-3 border-top" style="display:none;">
                    <p class="small text-muted mb-1">Descrizione</p>
                    <p id="modal-descrizione" class="mb-0" style="white-space:pre-wrap;font-size:.9rem;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                <a href="#" id="modal-btn-modifica" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i> Modifica
                </a>
                <a href="#" id="modal-btn-apri" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye mr-1"></i> Apri scheda
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal pianifica (drop dal pool) -->
<div class="modal fade" id="modal-pianifica" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success py-2">
                <h5 class="modal-title text-white">
                    <i class="fas fa-calendar-day mr-1"></i>
                    Pianifica — <span id="pian-giorno"></span>
                </h5>
            </div>
            <div class="modal-body">
                <p class="mb-0 font-weight-bold" id="pian-cliente"></p>
                <p class="small text-muted mb-3" id="pian-tipo"></p>
                <div class="form-row">
                    <div class="form-group col-sm-5 mb-2">
                        <label class="small">Orario <span class="text-danger">*</span></label>
                        <input type="time" id="pian-ora" class="form-control form-control-sm" step="900">
                    </div>
                    <div class="form-group col-sm-7 mb-2" id="pian-orario-sugg-wrap" style="display:none;">
                        <label class="small">&nbsp;</label>
                        <div id="pian-orario-sugg" class="small text-info pt-1"></div>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="small">Tecnico <span class="text-muted font-weight-normal">(opzionale)</span></label>
                    <select id="pian-tecnico" class="form-control form-control-sm"></select>
                    <div id="pian-suggerimento" class="mt-1"></div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-pian-annulla">Annulla</button>
                <button type="button" class="btn btn-success btn-sm" id="btn-pian-conferma">
                    <i class="fas fa-check mr-1"></i> Pianifica
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/it.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var _csrfToken = '<?= csrf_token() ?>';
    var _csrfHash  = '<?= csrf_hash() ?>';
    var oraInizio  = '<?= esc($oraInizio) ?>';
    var filtroTecnico = '';

    var tecnici              = <?= json_encode(array_map(fn($t) => ['id' => $t['id'], 'nome' => $t['nome'], 'cognome' => $t['cognome']], $tecnici)) ?>;
    var competenzePerTecnico = <?= json_encode($competenzePerTecnico) ?>;
    var tipiPerCodice        = <?= json_encode(array_map(fn($t) => ['id' => $t['id']], $tipiPerCodice)) ?>;
    var livelliNomi          = {1: 'Base', 2: 'Autonomo', 3: 'Referente'};

    // ---- Giorno selezionato ----
    function selezionaData(dateStr) {
        document.querySelectorAll('.fc-day-selected').forEach(function (el) {
            el.classList.remove('fc-day-selected');
        });
        document.querySelectorAll('[data-date="' + dateStr + '"]').forEach(function (el) {
            el.classList.add('fc-day-selected');
        });
        document.getElementById('form-data-giornata').value = dateStr;
        var parts = dateStr.split('-');
        document.getElementById('label-data-giornata').textContent = parts[2] + '/' + parts[1] + '/' + parts[0];
        document.getElementById('btn-genera-viaggio').disabled = false;
    }

    document.getElementById('calendario').addEventListener('click', function (e) {
        var cell = e.target.closest('.fc-col-header-cell[data-date]');
        if (cell) selezionaData(cell.dataset.date);
    });

    // ---- Filter buttons ----
    document.querySelectorAll('.btn-filtro').forEach(function (btn) {
        btn.addEventListener('click', function () {
            filtroTecnico = this.dataset.id;
            document.getElementById('form-tecnico-giornata').value = filtroTecnico;
            document.querySelectorAll('.btn-filtro').forEach(function (b) {
                if (b.dataset.id === '') {
                    b.classList.toggle('btn-secondary',         b.dataset.id === filtroTecnico);
                    b.classList.toggle('btn-outline-secondary', b.dataset.id !== filtroTecnico);
                } else {
                    b.style.opacity = (b.dataset.id === filtroTecnico || filtroTecnico === '') ? '1' : '.4';
                }
                b.classList.toggle('active', b.dataset.id === filtroTecnico);
            });
            calendar.refetchEvents();
        });
    });

    // ---- buildTecnicoSelect ----
    function buildTecnicoSelect(tipoCode, selectEl) {
        var tipoId      = (tipiPerCodice[tipoCode] || {}).id || 0;
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
        selectEl.innerHTML = html;
    }

    // ---- Pool Draggable ----
    new FullCalendar.Draggable(document.getElementById('pool-container'), {
        itemSelector: '.pool-card',
        eventData: function (cardEl) {
            return {
                id:       'pool-' + cardEl.dataset.id,
                title:    cardEl.dataset.cliente || ('#' + cardEl.dataset.id),
                duration: { minutes: parseInt(cardEl.dataset.durata) || 60 },
                extendedProps: { icona: cardEl.dataset.icona || 'fa-wrench' },
            };
        },
    });

    // ---- Modal pianifica ----
    var $modalPian    = $('#modal-pianifica');
    var pianTecnicoEl = document.getElementById('pian-tecnico');
    var pianSuggEl    = document.getElementById('pian-suggerimento');
    var pianOraEl     = document.getElementById('pian-ora');
    var pianOsuggWrap = document.getElementById('pian-orario-sugg-wrap');
    var pianOsuggEl   = document.getElementById('pian-orario-sugg');

    var pendingCard    = null;
    var pendingDateStr = null;

    function aggiornaOrarioSuggerito() {
        var tId = pianTecnicoEl.value;
        pianOsuggWrap.style.display = 'none';
        if (!tId || !pendingDateStr) return;
        fetch('<?= base_url('interventi/api/orario-suggerito') ?>'
            + '?tecnico_id=' + encodeURIComponent(tId)
            + '&data='       + encodeURIComponent(pendingDateStr))
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (!json.ora) return;
                var toMin = function (t) { var p = t.split(':'); return parseInt(p[0]) * 60 + parseInt(p[1]); };
                var suggerito = toMin(json.ora);
                var attuale   = toMin(pianOraEl.value || oraInizio);
                if (suggerito > attuale) {
                    pianOraEl.value = json.ora;
                    if (json.n_prev > 0) {
                        pianOsuggEl.textContent     = 'Dopo ' + json.n_prev + ' interv. precedenti';
                        pianOsuggWrap.style.display = '';
                    }
                }
            })
            .catch(function () {});
    }

    pianTecnicoEl.addEventListener('change', aggiornaOrarioSuggerito);

    function showModalPianifica(cardEl, dateStr, timeStr) {
        pendingCard    = cardEl;
        pendingDateStr = dateStr;

        var d      = new Date(dateStr + 'T12:00:00');
        var giorni = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
        var label  = giorni[d.getDay()] + ' '
                   + String(d.getDate()).padStart(2, '0') + '/'
                   + String(d.getMonth() + 1).padStart(2, '0');

        document.getElementById('pian-giorno').textContent  = label;
        document.getElementById('pian-cliente').textContent = cardEl.dataset.cliente || '—';
        document.getElementById('pian-tipo').textContent    = cardEl.dataset.tipoNome || '';
        pianOraEl.value             = timeStr || oraInizio;
        pianOsuggWrap.style.display = 'none';
        pianSuggEl.innerHTML        = '';
        buildTecnicoSelect(cardEl.dataset.tipo || '', pianTecnicoEl);

        var tipo      = cardEl.dataset.tipo      || '';
        var clienteId = cardEl.dataset.clienteId || 0;
        if (tipo) {
            fetch('<?= base_url('interventi/api/tecnico-consigliato') ?>'
                + '?tipo_intervento=' + encodeURIComponent(tipo)
                + '&cliente_id='      + encodeURIComponent(clienteId)
                + '&data='            + encodeURIComponent(dateStr))
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    if (!json.tecnico) return;
                    var t = json.tecnico;
                    var dettaglio = json.source === 'referente' ? 'Referente'
                                  : json.source === 'storico'   ? t.cnt + ' int. simili'
                                  :                               'disponibile';
                    pianSuggEl.innerHTML =
                        '<div class="alert alert-info py-1 px-2 mb-0 small">'
                        + '<i class="fas fa-lightbulb mr-1"></i>'
                        + 'Consigliato: <strong>' + t.cognome + ' ' + t.nome + '</strong>'
                        + ' <span class="text-muted">(' + dettaglio + ')</span>'
                        + ' <a href="#" class="ml-2"'
                        + ' onclick="document.getElementById(\'pian-tecnico\').value=\'' + t.tecnico_id + '\';'
                        + 'document.getElementById(\'pian-tecnico\').dispatchEvent(new Event(\'change\'));'
                        + 'this.parentElement.remove();return false;">Usa questo</a>'
                        + '</div>';
                })
                .catch(function () {});
        }

        $modalPian.modal('show');
    }

    $modalPian.on('hide.bs.modal', function () {
        pendingCard = pendingDateStr = null;
    });

    document.getElementById('btn-pian-annulla').addEventListener('click', function () {
        $modalPian.modal('hide');
    });

    document.getElementById('btn-pian-conferma').addEventListener('click', function () {
        if (!pendingCard || !pendingDateStr) return;
        var card         = pendingCard;
        var tecnicoAsseg = pianTecnicoEl.value || '';
        var ora          = pianOraEl.value || oraInizio;
        var dataPian     = pendingDateStr + 'T' + ora;
        var btnConf      = this;

        btnConf.disabled  = true;
        btnConf.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Salvo…';

        var fd = new FormData();
        fd.append('data_pianificata', dataPian);
        if (tecnicoAsseg) fd.append('tecnico_id', tecnicoAsseg);

        fetch('<?= base_url('interventi/') ?>' + card.dataset.id + '/pianifica', {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': _csrfHash },
            body:    fd,
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            if (json.ok) {
                if (json.csrf) _csrfHash = json.csrf;
                btnConf.disabled  = false;
                btnConf.innerHTML = '<i class="fas fa-check mr-1"></i> Pianifica';
                $modalPian.modal('hide');
                // Aggiorna badge gruppo tipo
                var group = card.closest('.mb-1');
                if (group) {
                    var groupBadge = group.querySelector('a .badge');
                    if (groupBadge) {
                        var gc = parseInt(groupBadge.textContent) - 1;
                        if (gc <= 0) { group.remove(); }
                        else { groupBadge.textContent = gc; }
                    }
                }
                card.remove();
                // Aggiorna badge totale
                var countEl = document.getElementById('pool-count');
                var n = parseInt(countEl.textContent || '1') - 1;
                countEl.textContent = n;
                if (n <= 0) {
                    document.getElementById('pool-container').innerHTML =
                        '<div class="pool-empty">'
                        + '<i class="fas fa-check-circle fa-2x d-block mb-2 text-success" style="opacity:.5;"></i>'
                        + 'Tutti pianificati</div>';
                }
                calendar.refetchEvents();
            } else {
                btnConf.disabled  = false;
                btnConf.innerHTML = '<i class="fas fa-check mr-1"></i> Pianifica';
                alert(json.msg || 'Errore durante il salvataggio.');
            }
        })
        .catch(function () {
            btnConf.disabled  = false;
            btnConf.innerHTML = '<i class="fas fa-check mr-1"></i> Pianifica';
            alert('Errore di rete.');
        });
    });

    // ---- FullCalendar ----
    var calendar = new FullCalendar.Calendar(document.getElementById('calendario'), {
        locale: 'it',
        initialView: window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'timeGridDay,timeGridWeek,dayGridMonth',
        },
        buttonText: { today: 'Oggi', day: 'Giorno', week: 'Settimana', month: 'Mese' },
        eventTextColor: '#1f2937',
        slotMinTime:  '07:00:00',
        slotMaxTime:  '20:00:00',
        slotDuration: '00:30:00',
        allDaySlot:   false,
        nowIndicator: true,
        height:       'auto',
        editable:     true,
        droppable:    true,
        longPressDelay: 300,
        eventDragStart: function () {
            $('body').addClass('fc-dragging');
            $('.tooltip').remove();
        },
        eventDragStop: function () {
            $('body').removeClass('fc-dragging');
            $('.tooltip').remove();
        },
        eventDrop: function (info) {
            var dt  = info.event.start;
            var pad = function (n) { return String(n).padStart(2, '0'); };
            var startLocal = dt.getFullYear() + '-' + pad(dt.getMonth() + 1) + '-' + pad(dt.getDate())
                           + ' ' + pad(dt.getHours()) + ':' + pad(dt.getMinutes()) + ':00';
            var postData   = { id: info.event.id, start: startLocal };
            postData[_csrfToken] = _csrfHash;
            $.ajax({
                url:      '<?= base_url('calendario/sposta') ?>',
                type:     'POST',
                data:     postData,
                headers:  { 'X-CSRF-TOKEN': _csrfHash },
                dataType: 'json',
                success: function (res) {
                    if (res && res.csrf) _csrfHash = res.csrf;
                    if (!res || !res.ok) { info.revert(); alert('Errore nel salvataggio.'); }
                },
                error: function (xhr) {
                    console.error('sposta error', xhr.status, xhr.responseText);
                    info.revert();
                    alert('Errore nel salvataggio. Lo spostamento è stato annullato.');
                },
            });
        },
        eventReceive: function (info) {
            var cardEl  = info.draggedEl;
            var start   = info.event.start;
            var pad     = function (n) { return String(n).padStart(2, '0'); };
            var dateStr = start.getFullYear() + '-' + pad(start.getMonth() + 1) + '-' + pad(start.getDate());
            var timeStr = pad(start.getHours()) + ':' + pad(start.getMinutes());
            info.event.remove();
            showModalPianifica(cardEl, dateStr, timeStr);
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            var url = '<?= base_url('calendario/eventi') ?>'
                    + '?start=' + fetchInfo.startStr.substring(0, 10)
                    + '&end='   + fetchInfo.endStr.substring(0, 10);
            if (filtroTecnico) url += '&tecnico_id=' + filtroTecnico;
            fetch(url)
                .then(function (r) { return r.json(); })
                .then(successCallback)
                .catch(failureCallback);
        },
        eventDidMount: function (info) {
            var p   = info.event.extendedProps;
            var tip = [info.event.title, p.citta, p.tecnico, p.tipo].filter(Boolean).join(' · ');
            $(info.el).tooltip({ title: tip, placement: 'top', trigger: 'hover', container: 'body' });
            $(info.el).on('click', function () { $(this).tooltip('hide'); });
        },
        eventWillUnmount: function (info) {
            $(info.el).tooltip('dispose');
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            var p   = info.event.extendedProps;
            var url = info.event.url;

            var badgeClass = { pianificato: 'badge-secondary', in_corso: 'badge-warning', completato: 'badge-success', annullato: 'badge-danger' };
            var statoLabel = { pianificato: 'Pianificato', in_corso: 'In corso', completato: 'Completato', annullato: 'Annullato' };

            var start   = info.event.start;
            var dataFmt = start
                ? start.toLocaleDateString('it-IT', { weekday:'long', day:'2-digit', month:'long', year:'numeric' })
                + ' alle ' + start.toLocaleTimeString('it-IT', { hour:'2-digit', minute:'2-digit' })
                : '—';

            $('#modal-icona').attr('class', 'fas ' + (p.icona || 'fa-tools') + ' mr-2');
            $('#modal-cliente').text(info.event.title);
            $('#modal-header').css('border-left', '4px solid ' + (info.event.backgroundColor || '#6c757d'));
            $('#modal-tipo').text(p.tipo || '—');
            $('#modal-tecnico').text(p.tecnico || 'Non assegnato');
            $('#modal-data').text(dataFmt);
            $('#modal-stato').html('<span class="badge ' + (badgeClass[p.stato] || 'badge-secondary') + '">' + (statoLabel[p.stato] || p.stato) + '</span>');
            var urlBase = url.split('?')[0];
            $('#modal-descrizione').text(p.descrizione || '');
            $('#modal-descrizione-wrap').toggle(!!p.descrizione);
            if (p.data_entro) {
                var ep = p.data_entro.split('-');
                $('#modal-entro').text(ep[2] + '/' + ep[1] + '/' + ep[0]);
                $('#modal-entro, #modal-entro-label').show();
            } else {
                $('#modal-entro, #modal-entro-label').hide();
            }
            $('#modal-btn-apri').attr('href', urlBase + '?from=calendario');
            $('#modal-btn-modifica').attr('href', urlBase.replace(/\/interventi\/(\d+)$/, '/interventi/$1/edit') + '?from=calendario');
            $('#modalIntervento').modal('show');
        },
        dateClick: function (info) {
            selezionaData(info.dateStr.substring(0, 10));
        },
        eventContent: function (info) {
            var p    = info.event.extendedProps;
            var time = info.timeText;
            function fmtDd(s) { var parts = s.split('-'); return parts[2] + '/' + parts[1]; }
            // position:relative sul wrapper permette al bottone × di posizionarsi in alto a destra
            var html = '<div style="position:relative;padding:2px 4px;overflow:hidden;line-height:1.25;">'
                     // padding-right:14px evita che il titolo finisca sotto il bottone ×
                     + '<div style="font-size:.78rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-right:14px;">'
                     +   '<i class="fas ' + (p.icona || 'fa-tools') + '" style="margin-right:3px;opacity:.8;"></i>'
                     +   time + ' &nbsp;' + info.event.title
                     + '</div>'
                     + '<div style="font-size:.72rem;opacity:.8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'
                     +   (p.tecnico || '')
                     +   (p.citta ? ' · ' + p.citta : '')
                     +   (p.data_entro ? ' · <i class="fas fa-clock" style="font-size:.65rem;"></i> ' + fmtDd(p.data_entro) : '')
                     + '</div>'
                     // Bottone × — data-id contiene l'id dell'intervento (viene letto dal click handler sotto)
                     + '<button class="btn-rimuovi-pian" data-id="' + info.event.id + '"'
                     + ' style="position:absolute;top:1px;right:2px;background:none;border:none;'
                     + 'color:rgba(255,255,255,.8);font-size:.95rem;padding:0 2px;line-height:1;cursor:pointer;"'
                     + ' title="Rimuovi pianificazione">&times;</button>'
                     + '</div>';
            return { html: html };
        },
    });
    calendar.render();

    // ---- Rimozione pianificazione (bottone × sugli eventi) ----
    // Uso la event delegation su #calendario invece di eventDidMount:
    // FullCalendar ridisegna gli eventi ad ogni refetch, e i listener
    // attaccati in eventDidMount andrebbero persi. Con la delega, basta
    // un solo listener sul contenitore padre che non viene mai ridisegnato.
    document.getElementById('calendario').addEventListener('click', function (e) {
        // Controllo se il click è finito sul bottone × (o su un suo figlio)
        var btn = e.target.closest('.btn-rimuovi-pian');
        if (!btn) return; // click su altra parte dell'evento → ignoro

        // Blocco la propagazione così FullCalendar non apre anche il modal dettaglio
        e.stopPropagation();
        e.preventDefault();

        var interventoId = btn.dataset.id;

        if (!confirm('Rimuovere questo intervento dalla pianificazione?\nTornerà nella coda "Da pianificare".')) return;

        // Chiamo il backend: azzera data_pianificata e tecnico_id, stato → da_pianificare
        fetch('<?= base_url('interventi/') ?>' + interventoId + '/annulla-pianificazione', {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': _csrfHash },
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            // CI4 rigenerà il token dopo ogni richiesta POST — aggiorno la variabile locale
            if (json.csrf) _csrfHash = json.csrf;
            if (json.ok) {
                // Ricarico la pagina per visualizzare nuovamente quanto tolto dalla pianificazione
                window.location.reload();
            } else {
                alert('Errore nella rimozione: ' + (json.msg || 'riprovare.'));
            }
        })
        .catch(function () {
            alert('Errore di rete. Riprovare.');
        });
    }, true); // true = capture phase, così intercetto prima di FullCalendar

    // Sidebar ridimensionabile con drag handle
    var poolPanel    = document.getElementById('pool-panel');
    var resizeHandle = document.getElementById('resize-handle');
    var savedW       = localStorage.getItem('pool-sidebar-width');
    if (savedW) poolPanel.style.width = savedW + 'px';
    var resizing = false, startX, startW;
    resizeHandle.addEventListener('mousedown', function (e) {
        resizing = true; startX = e.clientX; startW = poolPanel.offsetWidth;
        document.body.style.cursor     = 'col-resize';
        document.body.style.userSelect = 'none';
    });
    document.addEventListener('mousemove', function (e) {
        if (!resizing) return;
        var w = Math.max(180, Math.min(450, startW + e.clientX - startX));
        poolPanel.style.width = w + 'px';
    });
    document.addEventListener('mouseup', function () {
        if (!resizing) return;
        resizing = false;
        document.body.style.cursor     = '';
        document.body.style.userSelect = '';
        localStorage.setItem('pool-sidebar-width', poolPanel.offsetWidth);
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/calendario/index') ?>
<?= $this->endSection() ?>
