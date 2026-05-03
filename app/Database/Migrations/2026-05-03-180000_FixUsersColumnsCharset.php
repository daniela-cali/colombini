<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixUsersColumnsCharset extends Migration
{
    public function up(): void
    {
        $this->db->query("
            ALTER TABLE users
                MODIFY nome      VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                MODIFY cognome   VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                MODIFY telefono  VARCHAR(30)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                MODIFY ruolo     VARCHAR(20)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL
        ");
    }

    public function down(): void
    {
        // collation downgrade non necessario
    }
}
