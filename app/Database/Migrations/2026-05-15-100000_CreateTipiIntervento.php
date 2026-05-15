<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTipiIntervento extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'codice'         => ['type' => 'VARCHAR', 'constraint' => 50],
            'nome'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'durata_default' => ['type' => 'INT', 'unsigned' => true, 'default' => 60],
            'attivo'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'ordine'         => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('codice');
        $this->forge->createTable('tipi_intervento');

        $this->db->table('tipi_intervento')->insertBatch([
            ['codice' => 'piscine',     'nome' => 'Piscine',              'durata_default' => 120, 'ordine' => 1],
            ['codice' => 'addolcitori', 'nome' => 'Addolcitori',          'durata_default' => 60,  'ordine' => 2],
            ['codice' => 'acquedotti',  'nome' => 'Acquedotti',           'durata_default' => 90,  'ordine' => 3],
            ['codice' => 'commerciale', 'nome' => 'Richiesta Commerciale','durata_default' => 45,  'ordine' => 4],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('tipi_intervento', true);
    }
}
