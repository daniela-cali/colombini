<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCampiToVeicoli extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('veicoli', [
            'cambio_automatico' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'targa'],
            'carico_massimo'    => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'default' => null, 'after' => 'cambio_automatico'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('veicoli', ['cambio_automatico', 'carico_massimo']);
    }
}
