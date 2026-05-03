<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTecniciOrari extends Migration
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
            ],
            'giorno' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
            ],
            'ora_inizio' => ['type' => 'TIME'],
            'ora_fine'   => ['type' => 'TIME'],
            'attivo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['tecnico_id', 'giorno'], false, false, 'idx_tecnico_giorno');
        $this->forge->addForeignKey('tecnico_id', 'users', 'id', 'CASCADE', 'CASCADE', 'fk_tecnici_orari_user');

        $this->forge->createTable('tecnici_orari', false, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('tecnici_orari', true);
    }
}