<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRichiesteAssistenza extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tipo_intervento' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'luogo_intervento' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
            'richiedente' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'telefono_contatto' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'note' => [
                'type' => 'TEXT',
            ],
            'stato' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'nuova',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('richieste_assistenza');
    }

    public function down(): void
    {
        $this->forge->dropTable('richieste_assistenza', true);
    }
}
