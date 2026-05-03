<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGeoToClienti extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('clienti', [
            'lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'default'    => null,
                'after'      => 'cfisc',
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
        $this->forge->dropColumn('clienti', ['lat', 'lng', 'geocoded_at']);
    }
}