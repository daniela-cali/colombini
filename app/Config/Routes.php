<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('login',  'LoginController::loginView');
$routes->post('login', 'LoginController::loginAction');
$routes->get('logout', 'LoginController::logoutAction');

service('auth')->routes($routes, ['except' => ['login', 'logout']]);

// Portale cliente
$routes->group('portale', ['filter' => 'group:cliente'], function (RouteCollection $routes) {
    $routes->get('/',                'Portale::index');
    $routes->get('nuova-richiesta',  'Portale::nuovaRichiesta');
    $routes->post('nuova-richiesta', 'Portale::storeRichiesta');
    $routes->get('richiesta/(:num)', 'Portale::show/$1');
});

// Area gestionale (auth + admin-area filter)
$routes->group('', ['filter' => ['auth', 'admin-area']], function (RouteCollection $routes) {

    $routes->get('/',         'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    $routes->group('pianificazione', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',      'Pianificazione::index');
        $routes->post('salva', 'Pianificazione::salva');
    });

    $routes->group('ottimizzazione', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',       'Ottimizzazione::index');
        $routes->post('genera', 'Ottimizzazione::genera');
        $routes->post('salva',  'Ottimizzazione::salva');
    });

    $routes->group('viaggi', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',                   'Viaggi::index');
        $routes->get('pdf/(:segment)',      'Viaggi::pdfGiornata/$1');
        $routes->get('(:num)',              'Viaggi::show/$1');
        $routes->get('(:num)/pdf',          'Viaggi::pdfViaggio/$1');
        $routes->post('(:num)/autorizza',   'Viaggi::autorizza/$1');
        $routes->post('(:num)/riapri',      'Viaggi::riapri/$1');
        $routes->post('(:num)/annulla',     'Viaggi::annulla/$1');
        $routes->post('(:num)/veicolo',     'Viaggi::assegnaVeicolo/$1');
    });

    $routes->group('calendario', function (RouteCollection $routes) {
        $routes->get('/',                       'Calendario::index');
        $routes->get('eventi',                  'Calendario::eventi');
        $routes->post('sposta',                 'Calendario::sposta');
        $routes->post('genera-viaggio-giornata','Calendario::generaViaggioGiornata');
    });

    $routes->group('clienti', function (RouteCollection $routes) {
        $routes->get('/',                   'Clienti::index');
        $routes->get('new',                 'Clienti::create');
        $routes->post('/',                  'Clienti::store');
        $routes->get('import',              'Clienti::importView');
        $routes->post('import',             'Clienti::importAnalizza');
        $routes->get('import/mappa',        'Clienti::importMappa');
        $routes->post('import/esegui',      'Clienti::importEsegui');
        $routes->get('(:num)',              'Clienti::show/$1');
        $routes->get('(:num)/edit',         'Clienti::edit/$1');
        $routes->post('(:num)',             'Clienti::update/$1');
        $routes->post('(:num)/elimina',     'Clienti::delete/$1');
        $routes->post('(:num)/geocodifica',  'Clienti::geocodificaSingolo/$1');
        $routes->get('(:num)/portale',      'Clienti::creaPortale/$1');
        $routes->post('(:num)/portale',     'Clienti::storePortale/$1');
    });

    // ── Tecnici (anagrafica, solo staff) ─────────────────────────────────────
    $routes->group('tecnici', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',               'Tecnici::index');
        $routes->get('new',             'Tecnici::create');
        $routes->post('/',              'Tecnici::store');
        $routes->get('(:num)',          'Tecnici::show/$1');
        $routes->get('(:num)/edit',     'Tecnici::edit/$1');
        $routes->post('(:num)',         'Tecnici::update/$1');
        $routes->post('(:num)/elimina', 'Tecnici::delete/$1');
        $routes->post('(:num)/orari',       'Tecnici::orariUpdate/$1');
        $routes->post('(:num)/competenze', 'Tecnici::competenzeUpdate/$1');
    });

    // ── Sistema (configurazione) ──────────────────────────────────────────────
    $routes->group('sistema', ['filter' => 'solo-staff'], function (RouteCollection $routes) {

        $routes->group('tipi-intervento', function (RouteCollection $routes) {
            $routes->get('/',               'Sistema::tipiIntervento');
            $routes->get('new',             'Sistema::tipiInterventoCreate');
            $routes->post('/',              'Sistema::tipiInterventoStore');
            $routes->get('(:num)/edit',     'Sistema::tipiInterventoEdit/$1');
            $routes->post('(:num)',         'Sistema::tipiInterventoUpdate/$1');
            $routes->post('(:num)/elimina', 'Sistema::tipiInterventoDelete/$1');
        });

        $routes->group('veicoli', function (RouteCollection $routes) {
            $routes->get('/',               'Sistema::veicoli');
            $routes->get('new',             'Sistema::veicoliCreate');
            $routes->post('/',              'Sistema::veicoliStore');
            $routes->get('(:num)/edit',     'Sistema::veicoliEdit/$1');
            $routes->post('(:num)',         'Sistema::veicoliUpdate/$1');
            $routes->post('(:num)/elimina', 'Sistema::veicoliDelete/$1');
        });

    });

    $routes->group('impianti', function (RouteCollection $routes) {
        $routes->get('/',               'Impianti::index');
        $routes->get('piscine',         'Impianti::piscine');
        $routes->get('trattamento',     'Impianti::trattamento');
        $routes->get('new',             'Impianti::create');
        $routes->post('/',              'Impianti::store');
        $routes->get('(:num)',          'Impianti::show/$1');
        $routes->get('(:num)/edit',     'Impianti::edit/$1');
        $routes->post('(:num)',         'Impianti::update/$1');
        $routes->post('(:num)/elimina', 'Impianti::delete/$1');
    });

    $routes->group('interventi', function (RouteCollection $routes) {
        $routes->get('/',               'Interventi::index');
        $routes->get('new',             'Interventi::create');
        $routes->post('/',              'Interventi::store');
        $routes->get('(:num)',          'Interventi::show/$1');
        $routes->get('(:num)/edit',     'Interventi::edit/$1');
        $routes->post('(:num)',         'Interventi::update/$1');
        $routes->post('(:num)/elimina', 'Interventi::delete/$1');
        $routes->post('(:num)/chiudi',   'Interventi::chiudi/$1');
        $routes->post('(:num)/assegna', 'Interventi::assegnaTecnico/$1');
        $routes->get('(:num)/pdf',         'Interventi::pdf/$1');
        $routes->post('(:num)/invia-email', 'Interventi::inviaEmail/$1');
        $routes->post('(:num)/firma',       'Interventi::salvaFirma/$1');
        $routes->get('api/tecnico-consigliato', 'Interventi::apiTecnicoConsigliato');
        $routes->get('api/orario-suggerito',    'Interventi::apiOrarioSuggerito');
        $routes->post('(:num)/pianifica',              'Interventi::pianificaRapido/$1');
        $routes->post('(:num)/annulla-pianificazione', 'Interventi::annullaPianificazione/$1');
    });

    $routes->group('preventivi', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',               'Preventivi::index');
        $routes->get('new',             'Preventivi::create');
        $routes->post('/',              'Preventivi::store');
        $routes->get('(:num)',          'Preventivi::show/$1');
        $routes->get('(:num)/edit',     'Preventivi::edit/$1');
        $routes->post('(:num)',         'Preventivi::update/$1');
        $routes->post('(:num)/elimina', 'Preventivi::delete/$1');
    });

    $routes->group('richieste', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',                       'Richieste::index');
        $routes->get('(:num)',                  'Richieste::show/$1');
        $routes->post('(:num)/risposta',        'Richieste::storeRisposta/$1');
    });

    // ── Fornitori (anagrafica) ───────────────────────────────────────────────
    $routes->group('fornitori', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',               'Fornitori::index');
        $routes->get('new',             'Fornitori::create');
        $routes->post('/',              'Fornitori::store');
        $routes->get('(:num)',          'Fornitori::show/$1');
        $routes->get('(:num)/edit',     'Fornitori::edit/$1');
        $routes->post('(:num)',         'Fornitori::update/$1');
        $routes->post('(:num)/elimina', 'Fornitori::delete/$1');
    });

    // ── Magazzino (articoli + movimenti) ────────────────────────────────────
    $routes->group('magazzino', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',               'Magazzino::index');
        $routes->get('new',             'Magazzino::create');
        $routes->post('/',              'Magazzino::store');
        $routes->get('(:num)',          'Magazzino::show/$1');
        $routes->get('(:num)/edit',     'Magazzino::edit/$1');
        $routes->post('(:num)',         'Magazzino::update/$1');
        $routes->post('(:num)/elimina', 'Magazzino::delete/$1');

        $routes->group('movimenti', function (RouteCollection $routes) {
            $routes->get('/',               'MagMovimenti::index');
            $routes->get('new',             'MagMovimenti::create');
            $routes->post('/',              'MagMovimenti::store');
            $routes->get('(:num)',          'MagMovimenti::show/$1');
            $routes->post('(:num)/elimina', 'MagMovimenti::delete/$1');
        });
    });

    $routes->group('prodotti', function (RouteCollection $routes) {
        $routes->get('/',               'Prodotti::index');
        $routes->get('new',             'Prodotti::create');
        $routes->post('/',              'Prodotti::store');
        $routes->get('(:num)',          'Prodotti::show/$1');
        $routes->get('(:num)/edit',     'Prodotti::edit/$1');
        $routes->post('(:num)',         'Prodotti::update/$1');
        $routes->post('(:num)/elimina', 'Prodotti::delete/$1');
    });

    $routes->group('report', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',           'Report::index');
        $routes->get('interventi',  'Report::interventi');
        $routes->get('clienti',     'Report::clienti');
        $routes->get('impianti',    'Report::impianti');
    });

    $routes->group('profilo', function (RouteCollection $routes) {
        $routes->get('/',          'Profilo::index');
        $routes->post('/',         'Profilo::update');
        $routes->get('password',         'Profilo::changePassword');
        $routes->post('password',        'Profilo::updatePassword');
        $routes->post('versione-vista',  'Profilo::aggiornaVersioneVista');
    });

    $routes->group('impostazioni', ['filter' => 'solo-staff'], function (RouteCollection $routes) {
        $routes->get('/',  'Impostazioni\Generale::index');
        $routes->post('/', 'Impostazioni\Generale::update');

        $routes->get('parametri',  'Impostazioni\Generale::parametri');
        $routes->post('parametri', 'Impostazioni\Generale::salvaParametri');

        $routes->get('geocodifica',       'Impostazioni\Generale::geocodifica');
        $routes->post('geocodifica-step', 'Impostazioni\Generale::geocodificaStep');

        $routes->group('utenti-portale', function (RouteCollection $routes) {
            $routes->get('/',               'Impostazioni\Utenti::utentiPortale');
            $routes->get('new',             'Impostazioni\Utenti::creaUtentePortale');
            $routes->post('/',              'Impostazioni\Utenti::storeUtentePortale');
            $routes->get('(:num)/edit',     'Impostazioni\Utenti::editUtentePortale/$1');
            $routes->post('(:num)',         'Impostazioni\Utenti::updateUtentePortale/$1');
            $routes->post('(:num)/elimina', 'Impostazioni\Utenti::deleteUtentePortale/$1');
        });

        $routes->group('utenti-app', function (RouteCollection $routes) {
            $routes->get('/',               'Impostazioni\Utenti::utentiApp');
            $routes->get('new',             'Impostazioni\Utenti::creaUtenteApp');
            $routes->post('/',              'Impostazioni\Utenti::storeUtenteApp');
            $routes->get('(:num)/edit',     'Impostazioni\Utenti::editUtenteApp/$1');
            $routes->post('(:num)',         'Impostazioni\Utenti::updateUtenteApp/$1');
            $routes->post('(:num)/elimina', 'Impostazioni\Utenti::deleteUtenteApp/$1');
        });

        $routes->group('mag-categorie', function (RouteCollection $routes) {
            $routes->get('/',               'Impostazioni\Magazzino::categorie');
            $routes->post('/',              'Impostazioni\Magazzino::storeCategoria');
            $routes->get('(:num)/edit',     'Impostazioni\Magazzino::editCategoria/$1');
            $routes->post('(:num)',         'Impostazioni\Magazzino::updateCategoria/$1');
            $routes->post('(:num)/elimina', 'Impostazioni\Magazzino::deleteCategoria/$1');
        });

        $routes->group('mag-posizioni', function (RouteCollection $routes) {
            $routes->get('/',               'Impostazioni\Magazzino::posizioni');
            $routes->post('/',              'Impostazioni\Magazzino::storePosizione');
            $routes->get('(:num)/edit',     'Impostazioni\Magazzino::editPosizione/$1');
            $routes->post('(:num)',         'Impostazioni\Magazzino::updatePosizione/$1');
            $routes->post('(:num)/elimina', 'Impostazioni\Magazzino::deletePosizione/$1');
        });
    });
});