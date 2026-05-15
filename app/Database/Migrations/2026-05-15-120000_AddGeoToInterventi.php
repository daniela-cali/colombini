<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGeoToInterventi extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('interventi', [
            'citta' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'after'      => 'luogo_intervento',
            ],
            'lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'default'    => null,
                'after'      => 'citta',
            ],
            'lng' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'default'    => null,
                'after'      => 'lat',
            ],
            'geocoded_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'lng',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('interventi', ['citta', 'lat', 'lng', 'geocoded_at']);
    }
}
