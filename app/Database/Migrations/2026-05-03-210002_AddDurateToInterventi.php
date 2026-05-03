<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDurateToInterventi extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('interventi', [
            'durata_viaggio' => [
                'type'    => 'SMALLINT',
                'null'    => true,
                'default' => null,
                'after'   => 'data_pianificata',
            ],
            'durata_effettiva' => [
                'type'    => 'SMALLINT',
                'null'    => true,
                'default' => null,
                'after'   => 'durata_viaggio',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('interventi', ['durata_viaggio', 'durata_effettiva']);
    }
}