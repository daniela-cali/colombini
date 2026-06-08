<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMagMovimenti extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            /*
             * Tipo movimento:
             *   carico      — entrata merce da fornitore (DDT in entrata)
             *   scarico     — uscita per prelievo manuale interno
             *   consegna    — uscita a cliente con riferimento cliente/intervento
             *   rettifica_p — rettifica manuale positiva (correzione inventario)
             *   rettifica_m — rettifica manuale negativa (correzione inventario)
             *   inventario  — giacenza iniziale (usata all'import dal vecchio sistema)
             */
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['carico', 'scarico', 'consegna', 'rettifica_p', 'rettifica_m', 'inventario'],
                'null'       => false,
            ],
            'data' => [
                'type' => 'DATE',
                'null' => false,
            ],
            // Numero DDT, bolla o altro documento di riferimento
            'num_documento' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Numero DDT o documento di riferimento',
            ],
            // Valorizzato per tipo = carico
            'fornitore_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'comment'  => 'FK fornitori — valorizzato per tipo=carico',
            ],
            // Valorizzato per tipo = consegna
            'cliente_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'comment'  => 'FK clienti — valorizzato per tipo=consegna',
            ],
            // Opzionale: collega la consegna all'intervento specifico;
            // ON DELETE SET NULL preserva lo storico anche se l'intervento viene eliminato
            'intervento_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'comment'  => 'FK interventi — opzionale per tipo=consegna; SET NULL se l\'intervento viene eliminato',
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Utente che ha registrato il movimento
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'comment'  => 'FK users — chi ha registrato il movimento',
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
        $this->forge->addKey('tipo');
        $this->forge->addKey('data');
        $this->forge->addKey('cliente_id');
        $this->forge->addKey('intervento_id');
        $this->forge->createTable('mag_movimenti');

        $this->db->query('ALTER TABLE mag_movimenti ADD CONSTRAINT fk_magmov_fornitore
            FOREIGN KEY (fornitore_id) REFERENCES fornitori(id) ON DELETE SET NULL');
        $this->db->query('ALTER TABLE mag_movimenti ADD CONSTRAINT fk_magmov_cliente
            FOREIGN KEY (cliente_id) REFERENCES clienti(id) ON DELETE SET NULL');
        $this->db->query('ALTER TABLE mag_movimenti ADD CONSTRAINT fk_magmov_intervento
            FOREIGN KEY (intervento_id) REFERENCES interventi(id) ON DELETE SET NULL');
        $this->db->query('ALTER TABLE mag_movimenti ADD CONSTRAINT fk_magmov_user
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE mag_movimenti DROP FOREIGN KEY fk_magmov_fornitore');
        $this->db->query('ALTER TABLE mag_movimenti DROP FOREIGN KEY fk_magmov_cliente');
        $this->db->query('ALTER TABLE mag_movimenti DROP FOREIGN KEY fk_magmov_intervento');
        $this->db->query('ALTER TABLE mag_movimenti DROP FOREIGN KEY fk_magmov_user');
        $this->forge->dropTable('mag_movimenti');
    }
}
