<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVeicoli extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nome'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'targa'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'attivo'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('targa');
        $this->forge->createTable('veicoli');
    }

    public function down(): void
    {
        $this->forge->dropTable('veicoli', true);
    }
}
