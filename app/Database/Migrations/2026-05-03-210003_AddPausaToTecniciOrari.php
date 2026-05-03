<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPausaToTecniciOrari extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('tecnici_orari', [
            'pausa_inizio' => [
                'type'    => 'TIME',
                'null'    => true,
                'default' => null,
                'after'   => 'ora_fine',
            ],
            'pausa_fine' => [
                'type'    => 'TIME',
                'null'    => true,
                'default' => null,
                'after'   => 'pausa_inizio',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('tecnici_orari', ['pausa_inizio', 'pausa_fine']);
    }
}