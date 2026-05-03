<?php

namespace App\Commands;

use App\Models\ClienteModel;
use App\Services\GeocoderService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class GeocodeClienti extends BaseCommand
{
    protected $group       = 'Colombini';
    protected $name        = 'geocode:clienti';
    protected $description = 'Geocodifica i clienti con indirizzo ma senza coordinate (1 req/sec).';
    protected $usage       = 'geocode:clienti [--force]';
    protected $options     = [
        '--force' => 'Ri-geocodifica anche i clienti già geocodificati.',
    ];

    public function run(array $params): void
    {
        $model   = new ClienteModel();
        $geocoder = new GeocoderService();
        $force   = array_key_exists('force', $params) || CLI::getOption('force');

        $query = $model->where('indirizzo !=', null)
                       ->where('citta !=', null);

        if (! $force) {
            $query->where('geocoded_at', null);
        }

        $clienti = $query->findAll();
        $totale  = count($clienti);

        if ($totale === 0) {
            CLI::write('Nessun cliente da geocodificare.', 'yellow');
            return;
        }

        CLI::write("Clienti da processare: {$totale}", 'cyan');

        // Test connessione Nominatim via cURL
        $testGeo = (new GeocoderService())->geocode('Via Roma 1', 'Roma', '00100');
        if (! $testGeo) {
            CLI::write('ATTENZIONE: impossibile raggiungere Nominatim. Verifica che cURL sia abilitato e che il server abbia accesso a internet.', 'red');
            return;
        }
        CLI::write('Connessione a Nominatim OK.', 'green');

        $ok = $fail = 0;

        foreach ($clienti as $i => $c) {
            $n = $i + 1;
            CLI::write("[{$n}/{$totale}] {$c['codice']} — {$c['indirizzo']}, {$c['citta']}...", 'white');

            $geo = $geocoder->geocode(
                $c['indirizzo'],
                $c['citta'],
                $c['cap'] ?? ''
            );

            if ($geo) {
                $model->update($c['id'], [
                    'lat'         => $geo['lat'],
                    'lng'         => $geo['lng'],
                    'geocoded_at' => date('Y-m-d H:i:s'),
                ]);
                CLI::write("  ✓ {$geo['lat']}, {$geo['lng']}", 'green');
                $ok++;
            } else {
                CLI::write('  ✗ non trovato — verifica connessione o indirizzo', 'red');
                $fail++;
            }

            // rispetta il rate limit Nominatim (1 req/sec)
            if ($n < $totale) sleep(1);
        }

        CLI::newLine();
        CLI::write("Completato: {$ok} geocodificati, {$fail} non trovati.", $fail > 0 ? 'yellow' : 'green');
    }
}
