<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= esc($title ?? 'Portale Cliente') ?> | Colombini Piscine</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/custom.css') ?>">
    <style>
        :root { --nav-h: 56px; }
        body { background-color: #f0f4f8; padding-top: var(--nav-h); }

        /* ── Navbar ── */
        .p-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--nav-h);
            background: var(--clr-primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
            z-index: 1000;
        }
        .p-navbar .brand {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .45rem;
            white-space: nowrap;
        }
        .p-navbar .brand i { color: var(--clr-teal); font-size: 1.2rem; }
        .p-navbar .nav-actions { display: flex; align-items: center; gap: .5rem; }
        .p-navbar .btn-nav {
            color: rgba(255,255,255,.9);
            background: rgba(255,255,255,.12);
            border: none;
            border-radius: 6px;
            padding: .4rem .75rem;
            font-size: .85rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .35rem;
            white-space: nowrap;
            transition: background .15s;
        }
        .p-navbar .btn-nav:hover { background: rgba(255,255,255,.22); color: #fff; text-decoration: none; }
        .p-navbar .btn-nav.btn-nav-primary {
            background: var(--clr-teal);
            color: #fff;
        }
        .p-navbar .btn-nav.btn-nav-primary:hover { filter: brightness(1.1); }
        .p-navbar .nav-username {
            color: rgba(255,255,255,.7);
            font-size: .8rem;
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ── Content ── */
        .p-content {
            padding: 1.25rem 0 3rem;
            min-height: calc(100vh - var(--nav-h));
        }

        /* ── Cards ── */
        .p-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .p-card-header {
            background: var(--clr-primary);
            color: #fff;
            padding: .9rem 1.1rem;
            font-weight: 600;
            font-size: 1rem;
        }
        .p-card-header i { color: var(--clr-teal); margin-right: .4rem; }
        .p-card-body { padding: 1.1rem; }
        .p-card-footer {
            padding: .75rem 1.1rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ── Richiesta list (card-based on mobile) ── */
        .req-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,.07);
            padding: .9rem 1rem;
            margin-bottom: .75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            color: inherit;
            border-left: 4px solid var(--clr-teal);
            transition: box-shadow .15s;
        }
        .req-card:hover { box-shadow: 0 3px 10px rgba(0,0,0,.12); text-decoration: none; color: inherit; }
        .req-card .req-info h6 { margin: 0 0 .2rem; font-size: .95rem; font-weight: 600; }
        .req-card .req-info small { color: #6c757d; font-size: .78rem; }
        .req-card .req-arrow { color: #ced4da; font-size: 1.1rem; }

        /* ── Tipo-card selection ── */
        .tipo-card {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem .5rem;
            text-align: center;
            cursor: pointer;
            transition: all .18s;
            color: #6c757d;
            font-size: .82rem;
            user-select: none;
            height: 100%;
        }
        .tipo-card:hover { border-color: var(--clr-teal); color: var(--clr-teal); }
        .tipo-card.selected {
            border-color: var(--clr-primary);
            background: #e8f4fb;
            color: var(--clr-primary);
            font-weight: 600;
        }
        .tipo-card i { display: block; font-size: 1.6rem; margin-bottom: .4rem; }

        /* ── Footer ── */
        .p-footer {
            text-align: center;
            font-size: .75rem;
            color: #9aa0ac;
            padding: 1rem 0 1.5rem;
        }

        /* ── Touch targets ── */
        .btn { min-height: 44px; }
        input.form-control, textarea.form-control, select.form-control { min-height: 44px; }

        @media (max-width: 575px) {
            .p-navbar { padding: 0 .75rem; }
            .p-navbar .brand span { display: none; } /* mostra solo icona su schermi molto piccoli */
            .p-content { padding: 1rem 0 2rem; }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

<nav class="p-navbar">
    <?php
        $_u        = auth()->user();
        $_nome     = $_u ? (string) ($_u->nome ?: $_u->username) : 'Cliente';
    ?>
    <a href="<?= base_url('portale') ?>" class="brand">
        <i class="fas fa-water"></i>
        <span>Colombini Piscine</span>
    </a>
    <div class="nav-actions">
        <span class="nav-username d-none d-sm-block">
            <i class="fas fa-user-circle mr-1"></i><?= esc($_nome) ?>
        </span>
        <a href="<?= base_url('portale/nuova-richiesta') ?>" class="btn-nav btn-nav-primary">
            <i class="fas fa-plus"></i>
            <span class="d-none d-sm-inline">Nuova richiesta</span>
        </a>
        <a href="<?= base_url('logout') ?>" class="btn-nav" title="Esci">
            <i class="fas fa-sign-out-alt"></i>
            <span class="d-none d-sm-inline">Esci</span>
        </a>
    </div>
</nav>

<div class="container-fluid p-content" style="max-width:720px;">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mx-2">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mx-2">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>

</div>

<div class="p-footer">&copy; <?= date('Y') ?> Colombini S.n.c.</div>

<script src="<?= base_url('plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
