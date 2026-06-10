<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNazioneToClienti extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('clienti', [
            'nazione' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'ITALIA',
                'after'      => 'provincia',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('clienti', 'nazione');
    }
}
