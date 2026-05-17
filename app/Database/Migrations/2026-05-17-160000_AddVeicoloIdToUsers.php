<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVeicoloIdToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'veicolo_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'after'    => 'richiede_cambio_auto',
            ],
        ]);

        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_veicolo
            FOREIGN KEY (veicolo_id) REFERENCES veicoli(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_veicolo');
        $this->forge->dropColumn('users', 'veicolo_id');
    }
}
