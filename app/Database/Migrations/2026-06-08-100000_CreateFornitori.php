<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFornitori extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Ragione sociale per ditte; nome+cognome per persone fisiche
            'ragione_sociale' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Nome azienda (per ditte/società)',
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Nome (per fornitori persona fisica)',
            ],
            'cognome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Cognome (per fornitori persona fisica)',
            ],
            'referente' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Persona di contatto (per fornitori aziendali)',
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'default'    => null,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
            ],
            'indirizzo' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'default'    => null,
            ],
            'citta' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'provincia' => [
                'type'       => 'CHAR',
                'constraint' => 2,
                'null'       => true,
                'default'    => null,
            ],
            'cap' => [
                'type'       => 'CHAR',
                'constraint' => 5,
                'null'       => true,
                'default'    => null,
            ],
            'piva' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Partita IVA',
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // 1 = attivo, 0 = disattivato (nascosto dalle liste ma non eliminato)
            'attivo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = attivo, 0 = disattivato',
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('ragione_sociale');
        $this->forge->createTable('fornitori');
    }

    public function down(): void
    {
        $this->forge->dropTable('fornitori');
    }
}
