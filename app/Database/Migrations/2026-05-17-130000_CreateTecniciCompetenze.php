<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTecniciCompetenze extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tecnico_id'         => ['type' => 'INT', 'unsigned' => true],
            'tipo_intervento_id' => ['type' => 'INT', 'unsigned' => true],
            'livello'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 2], // 1=base 2=autonomo 3=referente
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['tecnico_id', 'tipo_intervento_id']);
        $this->forge->addForeignKey('tecnico_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tipo_intervento_id', 'tipi_intervento', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tecnici_competenze');
    }

    public function down(): void
    {
        $this->forge->dropTable('tecnici_competenze', true);
    }
}
