<?= $this->extend('layouts/admin') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
    <li class="breadcrumb-item active">Calendario</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
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
        },
        eventClick: function (info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
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
