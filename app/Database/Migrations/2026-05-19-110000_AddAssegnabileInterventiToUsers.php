<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAssegnabileInterventiToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'assegnabile_interventi' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'ruolo',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'assegnabile_interventi');
    }
}
