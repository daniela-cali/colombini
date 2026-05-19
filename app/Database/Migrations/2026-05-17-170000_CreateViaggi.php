<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateViaggi extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tecnico_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'veicolo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'data' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'stato' => [
                'type'       => 'ENUM',
                'constraint' => ['bozza', 'autorizzato', 'in_corso', 'completato'],
                'default'    => 'bozza',
            ],
            'distanza_totale' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'durata_totale' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'note' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('data');
        $this->forge->addKey('stato');
        $this->forge->createTable('viaggi');

        $this->db->query('ALTER TABLE viaggi ADD CONSTRAINT fk_viaggi_tecnico
            FOREIGN KEY (tecnico_id) REFERENCES users(id) ON DELETE RESTRICT');
        $this->db->query('ALTER TABLE viaggi ADD CONSTRAINT fk_viaggi_veicolo
            FOREIGN KEY (veicolo_id) REFERENCES veicoli(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE viaggi DROP FOREIGN KEY fk_viaggi_tecnico');
        $this->db->query('ALTER TABLE viaggi DROP FOREIGN KEY fk_viaggi_veicolo');
        $this->forge->dropTable('viaggi');
    }
}
