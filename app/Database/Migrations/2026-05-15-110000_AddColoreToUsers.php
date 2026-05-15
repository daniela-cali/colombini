<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColoreToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'colore' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null'       => true,
                'default'    => null,
                'after'      => 'telefono',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'colore');
    }
}
