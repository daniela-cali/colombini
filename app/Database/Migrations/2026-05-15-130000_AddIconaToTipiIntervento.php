<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIconaToTipiIntervento extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('tipi_intervento', [
            'icona' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nome',
            ],
        ]);

        $this->db->table('tipi_intervento')->update(['icona' => 'fa-swimming-pool'], ['codice' => 'piscine']);
        $this->db->table('tipi_intervento')->update(['icona' => 'fa-filter'],        ['codice' => 'addolcitori']);
        $this->db->table('tipi_intervento')->update(['icona' => 'fa-water'],         ['codice' => 'acquedotti']);
        $this->db->table('tipi_intervento')->update(['icona' => 'fa-handshake'],     ['codice' => 'commerciale']);
    }

    public function down(): void
    {
        $this->forge->dropColumn('tipi_intervento', 'icona');
    }
}
