<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateViaggioTappe extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'viaggio_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'intervento_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'ordine' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'posizionato_manualmente' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'distanza_da_precedente' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'durata_da_precedente' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
            'ora_arrivo_stimata' => [
                'type' => 'TIME',
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('intervento_id');
        $this->forge->createTable('viaggi_tappe');

        $this->db->query('ALTER TABLE viaggi_tappe ADD CONSTRAINT fk_tappe_viaggio
            FOREIGN KEY (viaggio_id) REFERENCES viaggi(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE viaggi_tappe ADD CONSTRAINT fk_tappe_intervento
            FOREIGN KEY (intervento_id) REFERENCES interventi(id) ON DELETE RESTRICT');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE viaggi_tappe DROP FOREIGN KEY fk_tappe_viaggio');
        $this->db->query('ALTER TABLE viaggi_tappe DROP FOREIGN KEY fk_tappe_intervento');
        $this->forge->dropTable('viaggi_tappe');
    }
}
