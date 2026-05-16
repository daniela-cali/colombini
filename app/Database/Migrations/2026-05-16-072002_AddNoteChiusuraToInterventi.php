<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoteChiusuraToInterventi extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('interventi', [
            'note_chiusura' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'note_interne',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('interventi', 'note_chiusura');
    }
}
