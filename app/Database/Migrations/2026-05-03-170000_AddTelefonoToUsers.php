<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTelefonoToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'default'    => null,
                'after'      => 'cognome',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'telefono');
    }
}
