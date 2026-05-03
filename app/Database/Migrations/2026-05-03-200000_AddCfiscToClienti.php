<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCfiscToClienti extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('clienti', [
            'cfisc' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'default'    => null,
                'after'      => 'piva',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('clienti', 'cfisc');
    }
}
