<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoToVeicoli extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('veicoli', [
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nome',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('veicoli', 'tipo');
    }
}
