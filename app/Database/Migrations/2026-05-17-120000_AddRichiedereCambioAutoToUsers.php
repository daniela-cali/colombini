<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRichiedereCambioAutoToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'richiede_cambio_auto' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'colore'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'richiede_cambio_auto');
    }
}