<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateInterventiClienteFK extends Migration
{
    public function up(): void
    {
        // Trova il nome esatto della FK esistente su interventi.cliente_id → users
        $row = $this->db->query("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA    = DATABASE()
              AND TABLE_NAME      = 'interventi'
              AND COLUMN_NAME     = 'cliente_id'
              AND REFERENCED_TABLE_NAME = 'users'
            LIMIT 1
        ")->getRowArray();

        if ($row) {
            $this->db->query("ALTER TABLE interventi DROP FOREIGN KEY `{$row['CONSTRAINT_NAME']}`");
        }

        // Azzera i riferimenti di test (puntavano a users, non più validi)
        $this->db->query("UPDATE interventi SET cliente_id = NULL");

        // Nuova FK verso clienti
        $this->db->query("
            ALTER TABLE interventi
              ADD CONSTRAINT fk_interventi_cliente
              FOREIGN KEY (cliente_id) REFERENCES clienti(id)
              ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }

    public function down(): void
    {
        $this->db->query("ALTER TABLE interventi DROP FOREIGN KEY fk_interventi_cliente");

        $this->db->query("UPDATE interventi SET cliente_id = NULL");

        $this->db->query("
            ALTER TABLE interventi
              ADD CONSTRAINT fk_interventi_cliente_users
              FOREIGN KEY (cliente_id) REFERENCES users(id)
              ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }
}
