<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropInterventoMaterialiCreateNote extends Migration
{
    public function up(): void
    {
        // Rimuove prima i FK poi la vecchia tabella per-intervento
        $this->db->query('ALTER TABLE intervento_materiali DROP FOREIGN KEY fk_intmat_intervento');
        $this->db->query('ALTER TABLE intervento_materiali DROP FOREIGN KEY fk_intmat_articolo');
        $this->db->query('ALTER TABLE intervento_materiali DROP FOREIGN KEY fk_intmat_movimento');
        $this->forge->dropTable('intervento_materiali');

        // Nuova tabella con scope cliente: persiste tra visite, lifecycle da portare → fornito
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cliente_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            // Collegato all'intervento al momento della pianificazione/creazione; NULL finché non assegnato
            'intervento_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'articolo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'quantita' => [
                'type'    => 'DECIMAL',
                'constraint' => '10,2',
                'null'    => true,
                'default' => null,
            ],
            // 0 = da portare, 1 = fornito
            'stato' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Auto-impostato a 1 dal model quando articolo_id è NULL
            'solo_note' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            // Predisposto per il futuro scarico di magazzino
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
        $this->forge->addKey('cliente_id');
        $this->forge->addKey('intervento_id');
        $this->forge->createTable('intervento_materiali_note');

        $this->db->query('ALTER TABLE intervento_materiali_note
            ADD CONSTRAINT fk_imn_cliente
            FOREIGN KEY (cliente_id) REFERENCES clienti(id)');

        // SET NULL: se l'intervento viene eliminato la riga rimane nel profilo cliente
        $this->db->query('ALTER TABLE intervento_materiali_note
            ADD CONSTRAINT fk_imn_intervento
            FOREIGN KEY (intervento_id) REFERENCES interventi(id) ON DELETE SET NULL');

        $this->db->query('ALTER TABLE intervento_materiali_note
            ADD CONSTRAINT fk_imn_articolo
            FOREIGN KEY (articolo_id) REFERENCES mag_articoli(id) ON DELETE SET NULL');

        $this->db->query('ALTER TABLE intervento_materiali_note
            ADD CONSTRAINT fk_imn_movimento
            FOREIGN KEY (movimento_id) REFERENCES mag_movimenti(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE intervento_materiali_note DROP FOREIGN KEY fk_imn_cliente');
        $this->db->query('ALTER TABLE intervento_materiali_note DROP FOREIGN KEY fk_imn_intervento');
        $this->db->query('ALTER TABLE intervento_materiali_note DROP FOREIGN KEY fk_imn_articolo');
        $this->db->query('ALTER TABLE intervento_materiali_note DROP FOREIGN KEY fk_imn_movimento');
        $this->forge->dropTable('intervento_materiali_note');

        // Ricrea la struttura originale
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'intervento_id'=> ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'articolo_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'quantita'     => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'default' => 1],
            'movimento_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'default' => null],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
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
}
