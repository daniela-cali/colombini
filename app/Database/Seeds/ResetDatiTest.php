<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

// Azzera tutti i dati operativi mantenendo solo utenti, veicoli, magazzino e impostazioni.
// Uso: php spark db:seed ResetDatiTest
class ResetDatiTest extends Seeder
{
    public function run(): void
    {
        $db = \Config\Database::connect();

        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ([
            'intervento_materiali_note',
            'interventi',
            'richieste_messaggi',
            'richieste_assistenza',
            'viaggi_tappe',
            'viaggi',
            'clienti',
        ] as $tabella) {
            $db->query("TRUNCATE TABLE `{$tabella}`");
            echo "  Svuotata: {$tabella}\n";
        }

        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        echo "\nReset completato. Utenti, veicoli, magazzino e impostazioni intatti.\n";
    }
}
