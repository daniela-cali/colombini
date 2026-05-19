<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCampiPianificazioneToInterventi extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('interventi', [
            'priorita'   => [
                'type'       => 'ENUM',
                'constraint' => ['urgente', 'ordinario', 'programmato'],
                'default'    => 'ordinario',
                'after'      => 'stato',
            ],
            'fissato'    => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'priorita',
            ],
            'ora_inizio' => [
                'type'    => 'TIME',
                'null'    => true,
                'default' => null,
                'after'   => 'fissato',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('interventi', ['priorita', 'fissato', 'ora_inizio']);
    }
}
