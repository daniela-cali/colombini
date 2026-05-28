<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRichiesteMessaggi extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'richiesta_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'testo' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('richiesta_id');
        $this->forge->createTable('richieste_messaggi');
    }

    public function down(): void
    {
        $this->forge->dropTable('richieste_messaggi');
    }
}
