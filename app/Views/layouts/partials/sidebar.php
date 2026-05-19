<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="<?= base_url('/') ?>" class="brand-link">
        <i class="fas fa-water ml-3 mr-2" style="color: var(--clr-teal); font-size:1.4rem;"></i>
        <span class="brand-text font-weight-bold">Colombini</span>
        <small class="brand-text text-sm ml-1" style="opacity:.7;">Piscine</small>
    </a>

    <div class="sidebar">
        <?php
            $_u        = auth()->user();
            $_username = $_u ? (string) $_u->username : 'Utente';
            $_ruolo    = $_u ? (string) $_u->ruolo    : '';
            $_isStaff  = $_ruolo !== 'tecnico';
        ?>
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x" style="color: var(--clr-azure);"></i>
            </div>
            <div class="info">
                <a href="<?= base_url('profilo') ?>" class="d-block">
                    <?= esc($_username) ?>
                </a>
                <small style="color: var(--clr-teal);">
                    <?= esc($_ruolo) ?>
                </small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Cruscotto -->
                <li class="nav-header">Cruscotto</li>
                <li class="nav-item">
                    <a href="<?= base_url('/') ?>" class="nav-link <?= (uri_string() === '/') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('calendario') ?>" class="nav-link <?= str_starts_with(uri_string(), 'calendario') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Calendario</p>
                    </a>
                </li>
                <?php if ($_isStaff): ?>
                <li class="nav-item">
                    <a href="<?= base_url('ottimizzazione') ?>" class="nav-link <?= str_starts_with(uri_string(), 'ottimizzazione') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-route"></i>
                        <p>Ottimizzazione <span class="badge badge-warning badge-sm ml-1" style="font-size:.6rem;vertical-align:middle;">Beta</span></p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Anagrafiche -->
                <li class="nav-header">Anagrafiche</li>
                <li class="nav-item">
                    <a href="<?= base_url('clienti') ?>" class="nav-link <?= str_starts_with(uri_string(), 'clienti') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clienti</p>
                    </a>
                </li>

                <!-- Impianti -->
                <li class="nav-header">Impianti</li>
                <li class="nav-item has-treeview <?= str_starts_with(uri_string(), 'impianti') ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= str_starts_with(uri_string(), 'impianti') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-swimming-pool"></i>
                        <p>Impianti <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('impianti/piscine') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Piscine</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('impianti/trattamento') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Trattamento Acqua</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Assistenza -->
                <li class="nav-header">Assistenza</li>
                <li class="nav-item">
                    <a href="<?= base_url('interventi') ?>" class="nav-link <?= str_starts_with(uri_string(), 'interventi') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>Interventi</p>
                    </a>
                </li>
                <?php if ($_isStaff): ?>
                <li class="nav-item">
                    <a href="<?= base_url('pianificazione') ?>" class="nav-link <?= str_starts_with(uri_string(), 'pianificazione') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Pianificazione</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('viaggi') ?>" class="nav-link <?= str_starts_with(uri_string(), 'viaggi') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-map-marked-alt"></i>
                        <p>Viaggi</p>
                    </a>
                </li>

                <!-- Commerciale -->
                <li class="nav-header">Commerciale</li>
                <li class="nav-item">
                    <a href="<?= base_url('preventivi') ?>" class="nav-link <?= str_starts_with(uri_string(), 'preventivi') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>Preventivi</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Magazzino -->
                <li class="nav-header">Magazzino</li>
                <li class="nav-item">
                    <a href="<?= base_url('prodotti') ?>" class="nav-link <?= str_starts_with(uri_string(), 'prodotti') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>Prodotti &amp; Ricambi</p>
                    </a>
                </li>

                <?php if ($_isStaff): ?>
                <!-- Report -->
                <li class="nav-header">Report</li>
                <li class="nav-item">
                    <a href="<?= base_url('report') ?>" class="nav-link <?= str_starts_with(uri_string(), 'report') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Statistiche</p>
                    </a>
                </li>

                <!-- Sistema -->
                <?php
                    $_isSistema = str_starts_with(uri_string(), 'sistema') || str_starts_with(uri_string(), 'impostazioni');
                ?>
                <li class="nav-header">Sistema</li>
                <li class="nav-item has-treeview <?= $_isSistema ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $_isSistema ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Configurazione <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('sistema/tecnici') ?>"
                               class="nav-link <?= str_starts_with(uri_string(), 'sistema/tecnici') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tecnici</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('sistema/tipi-intervento') ?>"
                               class="nav-link <?= str_starts_with(uri_string(), 'sistema/tipi-intervento') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tipi Intervento</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('sistema/veicoli') ?>"
                               class="nav-link <?= str_starts_with(uri_string(), 'sistema/veicoli') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Veicoli</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('impostazioni') ?>"
                               class="nav-link <?= str_starts_with(uri_string(), 'impostazioni') ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Impostazioni</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

            </ul>
        </nav>
    </div>
</aside>
