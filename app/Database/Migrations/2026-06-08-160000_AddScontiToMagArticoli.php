<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScontiToMagArticoli extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('mag_articoli', [
            'perc_sconto_1' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Sconto fornitore 1 (%)',
                'after'      => 'prezzo_acquisto',
            ],
            'perc_sconto_2' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Sconto fornitore 2 (%)',
                'after'      => 'perc_sconto_1',
            ],
            'perc_sconto_3' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Sconto fornitore 3 (%)',
                'after'      => 'perc_sconto_2',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('mag_articoli', ['perc_sconto_1', 'perc_sconto_2', 'perc_sconto_3']);
    }
}
