<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUltimaVersioneVista extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'ultima_versione_vista' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
                'after'      => 'colore',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'ultima_versione_vista');
    }
}