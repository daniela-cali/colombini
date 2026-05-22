<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFirmaTecnicoToInterventi extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('interventi', [
            'firma_tecnico' => [
                'type'    => 'MEDIUMTEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'firma_at',
            ],
            'firma_tecnico_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'firma_tecnico',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('interventi', ['firma_tecnico', 'firma_tecnico_at']);
    }
}
