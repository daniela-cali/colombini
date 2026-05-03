<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomFieldsToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'username',
            ],
            'cognome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'nome',
            ],
            'ruolo' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'tecnico', 'ufficio'],
                'default'    => 'tecnico',
                'after'      => 'cognome',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', ['nome', 'cognome', 'ruolo']);
    }
}
