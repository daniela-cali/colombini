<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMagPosizioni extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Es. "SCAFFALE BLU", "PANNELLO MURO", "SCAFFALE ELETTRICO"
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Nome zona fisica del magazzino (es. SCAFFALE BLU)',
            ],
            'attiva' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = visibile, 0 = nascosta',
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
        $this->forge->createTable('mag_posizioni');

        // Posizioni fisiche importate dal vecchio sistema
        $this->db->table('mag_posizioni')->insertBatch([
            ['nome' => 'BIANCO',             'attiva' => 1],
            ['nome' => 'BLU',                'attiva' => 1],
            ['nome' => 'GIALLO',             'attiva' => 1],
            ['nome' => 'ROSSO',              'attiva' => 1],
            ['nome' => 'VERDE',              'attiva' => 1],
            ['nome' => 'PANNELLO MURO',      'attiva' => 1],
            ['nome' => 'CARTONE',            'attiva' => 1],
            ['nome' => 'SCAFFALE',           'attiva' => 1],
            ['nome' => 'SCAFFALE ELETTRICO', 'attiva' => 1],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('mag_posizioni');
    }
}
