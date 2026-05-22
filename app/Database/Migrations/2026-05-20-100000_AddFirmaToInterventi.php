<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFirmaToInterventi extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('interventi', [
            'firma_cliente' => [
                'type'    => 'MEDIUMTEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'note_chiusura',
            ],
            'firma_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'firma_cliente',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('interventi', ['firma_cliente', 'firma_at']);
    }
}
