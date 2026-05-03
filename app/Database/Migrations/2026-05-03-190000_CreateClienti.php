<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClienti extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_external' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
            ],
            'codice' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['societa', 'persona_fisica'],
                'default'    => 'societa',
            ],
            'ragsoc' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'cognome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'piva' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
                'default'    => null,
            ],
            'indirizzo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
            'citta' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'cap' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
            ],
            'provincia' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
                'default'    => null,
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
            'note' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'contatti' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'dt_import' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
            ],
            'stato' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('codice', 'uq_codice');
        $this->forge->addKey('id_external', false, false, 'idx_id_external');
        $this->forge->addKey('user_id', false, false, 'idx_user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_clienti_user');

        $this->forge->createTable('clienti', false, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('clienti', true);
    }
}
