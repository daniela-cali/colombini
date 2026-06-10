<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDataEntroToInterventi extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('interventi', [
            'data_entro' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'after'   => 'data_pianificata',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('interventi', 'data_entro');
    }
}
