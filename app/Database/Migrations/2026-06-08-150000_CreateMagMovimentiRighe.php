<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMagMovimentiRighe extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'movimento_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'comment'  => 'FK mag_movimenti — testata di appartenenza',
            ],
            'articolo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'comment'  => 'FK mag_articoli',
            ],
            // Sempre positivo; il segno (+/-) è determinato dal tipo del movimento padre
            'quantita' => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
                'null'     => false,
                'comment'  => 'Quantità; il segno è determinato dal tipo del movimento (carico=+, scarico=-)',
            ],
            // Valorizzato per carichi e rettifiche; null per scarichi e consegne
            'costo_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Costo unitario al momento del carico; null per scarichi e consegne',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('movimento_id');
        $this->forge->addKey('articolo_id');
        $this->forge->createTable('mag_movimenti_righe');

        // CASCADE: se si cancella la testata, si cancellano anche le righe
        $this->db->query('ALTER TABLE mag_movimenti_righe ADD CONSTRAINT fk_magrighe_movimento
            FOREIGN KEY (movimento_id) REFERENCES mag_movimenti(id) ON DELETE CASCADE');
        // RESTRICT: non si può cancellare un articolo che ha movimenti registrati
        $this->db->query('ALTER TABLE mag_movimenti_righe ADD CONSTRAINT fk_magrighe_articolo
            FOREIGN KEY (articolo_id) REFERENCES mag_articoli(id) ON DELETE RESTRICT');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE mag_movimenti_righe DROP FOREIGN KEY fk_magrighe_movimento');
        $this->db->query('ALTER TABLE mag_movimenti_righe DROP FOREIGN KEY fk_magrighe_articolo');
        $this->forge->dropTable('mag_movimenti_righe');
    }
}
