<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="<?= base_url('/') ?>" class="brand-link">
        <i class="fas fa-water ml-3 mr-2" style="color: var(--clr-teal); font-size:1.4rem;"></i>
        <span class="brand-text font-weight-bold">Colombini Snc</span>
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
                <!-- Ottimizzazione nascosta: rotta intatta, visibile quando sarà in beta -->
                <?php /* if ($_isStaff): ?>
                <li class="nav-item">
                    <a href="<?= base_url('ottimizzazione') ?>" class="nav-link">
                        <i class="nav-icon fas fa-route"></i>
                        <p>Ottimizzazione</p>
                    </a>
                </li>
                <?php endif; */ ?>

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
                <li class="nav-item">
                    <a href="<?= base_url('impianti/piscine') ?>" class="nav-link <?= str_starts_with(uri_string(), 'impianti/piscine') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-swimming-pool"></i>
                        <p>Piscine</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('impianti/trattamento') ?>" class="nav-link <?= str_starts_with(uri_string(), 'impianti/trattamento') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tint"></i>
                        <p>Trattamento Acqua</p>
                    </a>
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
                    <?php $_nuoveRichieste = (new \App\Models\RichiestaModel())->where('stato', 'nuova')->countAllResults(); ?>
                    <a href="<?= base_url('richieste') ?>" class="nav-link <?= str_starts_with(uri_string(), 'richieste') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-inbox"></i>
                        <p>Richieste
                            <?php if ($_nuoveRichieste > 0): ?>
                                <span class="badge badge-info right"><?= $_nuoveRichieste ?></span>
                            <?php endif; ?>
                        </p>
                    </a>
                </li>
                <!-- Pianificazione nascosta: rotta intatta, accessibile via URL diretto -->
                <?php /* ?>
                <li class="nav-item">
                    <a href="<?= base_url('pianificazione') ?>" class="nav-link">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Pianificazione</p>
                    </a>
                </li>
                <?php */ ?>
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
                <?php
                    $_magPiscine     = str_starts_with(uri_string(), 'magazzino/piscine');
                    $_magTrattamento = str_starts_with(uri_string(), 'magazzino/trattamento');
                ?>
                <li class="nav-header">Magazzino</li>
                <li class="nav-item">
                    <a href="<?= base_url('prodotti') ?>" class="nav-link <?= str_starts_with(uri_string(), 'prodotti') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-flask"></i>
                        <p>Prodotti chimici</p>
                    </a>
                </li>
                <li class="nav-item has-treeview <?= $_magPiscine ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $_magPiscine ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-swimming-pool"></i>
                        <p>Piscine <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('magazzino/piscine/impianti') ?>"
                               class="nav-link <?= uri_string() === 'magazzino/piscine/impianti' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Impianti</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('magazzino/piscine/ricambi') ?>"
                               class="nav-link <?= uri_string() === 'magazzino/piscine/ricambi' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ricambi</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview <?= $_magTrattamento ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $_magTrattamento ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tint"></i>
                        <p>Trattamento Acqua <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('magazzino/trattamento/impianti') ?>"
                               class="nav-link <?= uri_string() === 'magazzino/trattamento/impianti' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Impianti</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('magazzino/trattamento/ricambi') ?>"
                               class="nav-link <?= uri_string() === 'magazzino/trattamento/ricambi' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ricambi</p>
                            </a>
                        </li>
                    </ul>
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
                <li class="nav-header">Sistema</li>
                <li class="nav-item">
                    <a href="<?= base_url('sistema/tecnici') ?>"
                       class="nav-link <?= str_starts_with(uri_string(), 'sistema/tecnici') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-hard-hat"></i>
                        <p>Tecnici</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('sistema/tipi-intervento') ?>"
                       class="nav-link <?= str_starts_with(uri_string(), 'sistema/tipi-intervento') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Tipi Intervento</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('sistema/veicoli') ?>"
                       class="nav-link <?= str_starts_with(uri_string(), 'sistema/veicoli') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Veicoli</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('impostazioni') ?>"
                       class="nav-link <?= str_starts_with(uri_string(), 'impostazioni') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Impostazioni</p>
                    </a>
                </li>
                <?php endif; ?>

            </ul>
        </nav>
    </div>
</aside>
