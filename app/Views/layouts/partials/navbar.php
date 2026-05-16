<nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url('/') ?>" class="nav-link">Home</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <?php
            $_u        = auth()->user();
            $_username = $_u ? (string) $_u->username : 'Utente';
            $_clNav    = @file_get_contents(ROOTPATH . 'CHANGELOG.md');
            $_verNav   = '';
            if ($_clNav && preg_match('/^## \[(\d+\.\d+\.\d+)\]/m', $_clNav, $_mNav)) {
                $_verNav = 'v' . $_mNav[1];
            }
        ?>
        <?php if ($_verNav): ?>
        <li class="nav-item d-none d-sm-inline-flex align-items-center mr-2">
            <span style="font-size:.7rem; opacity:.7; cursor:pointer; letter-spacing:.03em; color:#fff;"
                  data-toggle="modal" data-target="#modalChangelog"
                  title="Visualizza changelog">
                <?= esc($_verNav) ?>
            </span>
        </li>
        <?php endif; ?>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle mr-1"></i>
                <?= esc($_username) ?>
                <i class="fas fa-caret-down ml-1"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?= base_url('profilo') ?>" class="dropdown-item">
                    <i class="fas fa-user-cog mr-2"></i> Profilo
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('logout') ?>" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Esci
                </a>
            </div>
        </li>
    </ul>
</nav>