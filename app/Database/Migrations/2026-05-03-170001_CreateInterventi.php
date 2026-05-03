<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterventi extends Migration
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
            'richiesta_id' => [
                'type'     => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
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
            'cliente_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
            'tecnico_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
            'data_pianificata' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'data_completamento' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'descrizione' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'note_interne' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'stato' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pianificato',
            ],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('tecnico_id');
        $this->forge->addKey('cliente_id');
        $this->forge->addForeignKey('richiesta_id', 'richieste_assistenza', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('cliente_id',   'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('tecnico_id',   'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('interventi');
    }

    public function down(): void
    {
        $this->forge->dropTable('interventi', true);
    }
}
