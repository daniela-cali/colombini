<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMagCategorie extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Es. "Ricambi piscine", "Ricambi addolcitori", "Prodotti chimici"
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'Nome categoria visualizzato nei filtri e nei select',
            ],
            'descrizione' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Sequenza di apparizione nei filtri/select interni alla pagina magazzino
            'ordine' => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
                'default'  => 0,
                'comment'  => 'Ordine di apparizione nei filtri interni alla pagina',
            ],
            'attiva' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = visibile nei filtri, 0 = nascosta',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('mag_categorie');

        // Categorie iniziali
        $this->db->table('mag_categorie')->insertBatch([
            ['nome' => 'Ricambi piscine',    'ordine' => 1, 'attiva' => 1],
            ['nome' => 'Ricambi addolcitori', 'ordine' => 2, 'attiva' => 1],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('mag_categorie');
    }
}
