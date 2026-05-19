<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGeocodificaFallitaToClienti extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('clienti', [
            'geocodifica_fallita' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'geocoded_at',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('clienti', 'geocodifica_fallita');
    }
}
