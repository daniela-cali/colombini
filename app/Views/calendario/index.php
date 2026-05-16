<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Calendario</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    #calendario { min-height: 600px; }
    .fc-event { cursor: pointer; }
    .fc-event-title { font-weight: 500; font-size: .8rem; }
    .fc-timegrid-event .fc-event-title { white-space: normal; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body p-2 p-md-3">
        <div id="calendario"></div>
    </div>
</div>

<!-- Modal dettaglio intervento -->
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/it.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendar = new FullCalendar.Calendar(document.getElementById('calendario'), {
        locale: 'it',
        initialView: window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'timeGridDay,timeGridWeek,dayGridMonth',
        },
        buttonText: {
            today: 'Oggi',
            day:   'Giorno',
            week:  'Settimana',
            month: 'Mese',
        },
        eventTextColor: '#1f2937',
        slotMinTime:  '07:00:00',
        slotMaxTime:  '20:00:00',
        slotDuration: '00:30:00',
        allDaySlot:   false,
        nowIndicator: true,
        height:       'auto',
        eventSources: [{
            url: '<?= base_url('calendario/eventi') ?>',
            failure: function () {
                alert('Errore nel caricamento degli eventi del calendario.');
            },
        }],
        eventDidMount: function (info) {
            var p   = info.event.extendedProps;
            var tip = [p.tecnico, p.tipo, p.stato].filter(Boolean).join(' · ');
            $(info.el).tooltip({ title: tip, placement: 'top', trigger: 'hover', container: 'body' });
            $(info.el).on('click', function () { $(this).tooltip('hide'); });
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            var p   = info.event.extendedProps;
            var url = info.event.url;

            // Badge stato
            var badgeClass = { pianificato: 'badge-secondary', in_corso: 'badge-warning', completato: 'badge-success', annullato: 'badge-danger' };
            var statoLabel = { pianificato: 'Pianificato', in_corso: 'In corso', completato: 'Completato', annullato: 'Annullato' };

            // Data formattata
            var start = info.event.start;
            var dataFmt = start ? start.toLocaleDateString('it-IT', { weekday:'long', day:'2-digit', month:'long', year:'numeric' })
                                + ' alle ' + start.toLocaleTimeString('it-IT', { hour:'2-digit', minute:'2-digit' })
                                : '—';

            // Popola modal
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
            $('#modal-btn-apri').attr('href', urlBase + '?from=calendario');
            $('#modal-btn-modifica').attr('href', urlBase.replace(/\/interventi\/(\d+)$/, '/interventi/$1/edit') + '?from=calendario');

            $('#modalIntervento').modal('show');
        },
        eventContent: function (info) {
            var p    = info.event.extendedProps;
            var time = info.timeText;
            var html = '<div style="padding:2px 4px;overflow:hidden;line-height:1.25;">'
                     + '<div style="font-size:.78rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'
                     +   '<i class="fas ' + (p.icona || 'fa-tools') + '" style="margin-right:3px;opacity:.8;"></i>'
                     +   time + ' &nbsp;' + info.event.title
                     + '</div>'
                     + '<div style="font-size:.72rem;opacity:.8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + (p.tecnico || '') + '</div>'
                     + '</div>';
            return { html: html };
        },
    });
    calendar.render();
});
</script>
<?= $this->endSection() ?>

<?= $this->section('help') ?>
<?= $this->include('help/calendario/index') ?>
<?= $this->endSection() ?>
