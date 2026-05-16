<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= esc($title ?? 'Gestione') ?> | Colombini Piscine</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/custom.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?= $this->include('layouts/partials/navbar') ?>
    <?= $this->include('layouts/partials/sidebar') ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= esc($page_title ?? '') ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <?= $this->renderSection('breadcrumb') ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </section>
    </div>

    <?= $this->include('layouts/partials/footer') ?>

</div>

<!-- Modal novità versione -->
<?php
    $_cl = @file_get_contents(ROOTPATH . 'CHANGELOG.md');
    $_versioneCorrente = '';
    $_sezioneNovita    = '';
    if ($_cl && preg_match('/^## \[(\d+\.\d+\.\d+)\]/m', $_cl, $_mv)) {
        $_versioneCorrente = $_mv[1];
        if (preg_match('/^## \[' . preg_quote($_versioneCorrente, '/') . '\][^\n]*\n(.*?)(?=^## \[|\z)/ms', $_cl, $_ms)) {
            $_sezioneNovita = trim($_ms[1]);
        }
    }
    $_utenteCorrente = auth()->user();
    $_versioneUtente = $_utenteCorrente ? ($_utenteCorrente->ultima_versione_vista ?? '') : $_versioneCorrente;
    $_mostraNovita   = $_versioneCorrente && $_versioneUtente !== $_versioneCorrente;
?>
<?php if ($_mostraNovita): ?>
<div class="modal fade" id="modalNovita" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--clr-teal);color:#fff;">
                <h5 class="modal-title">
                    <i class="fas fa-star mr-2"></i>Novità — v<?= esc($_versioneCorrente) ?>
                </h5>
            </div>
            <div class="modal-body" id="novita-body" style="font-size:.9rem;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="btn-novita-ok">
                    <i class="fas fa-check mr-1"></i> Ho capito, grazie!
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal changelog completo -->
<div class="modal fade" id="modalChangelog" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--clr-teal);color:#fff;">
                <h5 class="modal-title"><i class="fas fa-history mr-2"></i>Changelog</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="changelog-body" style="font-size:.9rem;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- Pulsante guida contestuale -->
<button type="button" id="btn-guida" data-toggle="modal" data-target="#modalGuida"
        title="Guida" aria-label="Apri guida">
    <i class="fas fa-question"></i>
</button>

<!-- Modal guida -->
<div class="modal fade" id="modalGuida" tabindex="-1" role="dialog" aria-labelledby="guidaTitle">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--clr-teal);color:#fff;">
                <h5 class="modal-title" id="guidaTitle">
                    <i class="fas fa-question-circle mr-2"></i>
                    Guida — <?= esc($page_title ?? 'Colombini Piscine') ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body guida-body">
                <?php $helpContent = $this->renderSection('help'); ?>
                <?php if (trim($helpContent)): ?>
                    <?= $helpContent ?>
                <?php else: ?>
                    <p class="text-muted">Nessuna guida disponibile per questa pagina.</p>
                    <p>Per assistenza contatta l'amministratore del sistema.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked@9.1.6/marked.min.js"></script>
<script src="<?= base_url('plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<script src="<?= base_url('dist/js/adminlte.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
<script>
setTimeout(function () {
    $('.alert-success').fadeOut(600);
}, 4000);

$(function () {
    $('input[type="password"]').each(function () {
        var $input = $(this);
        if ($input.data('pwd-toggle-init')) return;
        $input.data('pwd-toggle-init', true);

        if (!$input.parent().hasClass('input-group')) {
            $input.wrap('<div class="input-group"></div>');
        }

        if ($input.parent().find('.input-group-append').length) return;

        var $append = $('<div class="input-group-append"><span class="input-group-text" style="cursor:pointer;" title="Mostra/nascondi password"><i class="fas fa-eye"></i></span></div>');
        $input.parent().append($append);

        $append.on('click', function () {
            var icon = $(this).find('i');
            $input[0].type = $input[0].type === 'password' ? 'text' : 'password';
            icon.toggleClass('fa-eye fa-eye-slash');
        });
    });

    // Modal changelog completo
    var _changelogMd = <?= json_encode($_cl ?: '') ?>;
    $('#modalChangelog').on('show.bs.modal', function () {
        var body = document.getElementById('changelog-body');
        if (!body.innerHTML.trim()) {
            body.innerHTML = marked.parse(_changelogMd);
        }
    });

    // Modal novità
    <?php if ($_mostraNovita): ?>
    var _novitaMd      = <?= json_encode($_sezioneNovita) ?>;
    var _versioneNuova = <?= json_encode($_versioneCorrente) ?>;
    document.getElementById('novita-body').innerHTML = marked.parse(_novitaMd);
    $('#modalNovita').modal('show');
    $('#btn-novita-ok').on('click', function () {
        $.post('<?= base_url('profilo/versione-vista') ?>', {
            versione:               _versioneNuova,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function () {
            $('#modalNovita').modal('hide');
        });
    });
    <?php endif; ?>
});
</script>
</body>
</html>
