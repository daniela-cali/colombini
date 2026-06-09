<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterventoMateriali extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'intervento_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'articolo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'quantita' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 1,
            ],
            // Predisposto per la futura generazione automatica del movimento di scarico.
            'movimento_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('intervento_id');
        $this->forge->addKey('articolo_id');
        $this->forge->createTable('intervento_materiali');

        $this->db->query('ALTER TABLE intervento_materiali
            ADD CONSTRAINT fk_intmat_intervento
            FOREIGN KEY (intervento_id) REFERENCES interventi(id) ON DELETE CASCADE');

        $this->db->query('ALTER TABLE intervento_materiali
            ADD CONSTRAINT fk_intmat_articolo
            FOREIGN KEY (articolo_id) REFERENCES mag_articoli(id)');

        $this->db->query('ALTER TABLE intervento_materiali
            ADD CONSTRAINT fk_intmat_movimento
            FOREIGN KEY (movimento_id) REFERENCES mag_movimenti(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE intervento_materiali DROP FOREIGN KEY fk_intmat_intervento');
        $this->db->query('ALTER TABLE intervento_materiali DROP FOREIGN KEY fk_intmat_articolo');
        $this->db->query('ALTER TABLE intervento_materiali DROP FOREIGN KEY fk_intmat_movimento');
        $this->forge->dropTable('intervento_materiali');
    }
}
