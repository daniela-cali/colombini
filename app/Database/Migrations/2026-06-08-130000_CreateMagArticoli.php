<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMagArticoli extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Codice alfanumerico univoco (es. "01001176", "00295") — usato come chiave di raccordo nell'import dal vecchio sistema
            'cod_articolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'Codice univoco; chiave di raccordo nell\'import dal vecchio sistema',
            ],
            'descrizione' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
            ],
            // N=numero/pezzi, L=litri, KG=chilogrammi, M=metri
            'unita_misura' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'N',
                'comment'    => 'N=pezzi, L=litri, KG=kg, M=metri',
            ],
            'categoria_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'comment'  => 'FK mag_categorie',
            ],
            'fornitore_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'comment'  => 'FK fornitori — fornitore principale dell\'articolo',
            ],
            // Zona fisica del magazzino (es. SCAFFALE BLU)
            'posizione_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'comment'  => 'FK mag_posizioni — zona fisica del magazzino',
            ],
            // Posizione precisa all'interno della zona (es. "E 1", "F 12")
            'coordinate' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Posizione precisa nello scaffale (es. E 1, F 12)',
            ],
            // Soglia minima: se giacenza_corrente scende sotto questo valore, l'articolo appare nel riordino
            'scorta_min' => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
                'default'  => 0,
                'comment'  => 'Soglia minima; sotto questo valore l\'articolo appare nel riordino',
            ],
            // Quantità consigliata da ordinare quando si raggiunge la scorta minima
            'lotto_riordino' => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
                'default'  => 0,
                'comment'  => 'Quantità suggerita per il riordino',
            ],
            // Cache aggiornata atomicamente ad ogni movimento; la fonte di verità è mag_movimenti
            'giacenza_corrente' => [
                'type'    => 'SMALLINT',
                'default' => 0,
                'comment' => 'Cache giacenza aggiornata ad ogni movimento; fonte di verità = mag_movimenti',
            ],
            // Aggiornato all'ultimo carico registrato
            'prezzo_acquisto' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'unsigned'   => true,
                'default'    => '0.00',
                'comment'    => 'Ultimo prezzo di acquisto registrato',
            ],
            'prezzo_vendita' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'unsigned'   => true,
                'default'    => '0.00',
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Soft delete: sostituisce la tabella t_articoli_canc del vecchio sistema
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Soft delete — sostituisce t_articoli_canc del vecchio sistema',
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
        $this->forge->addUniqueKey('cod_articolo');
        $this->forge->addKey('categoria_id');
        $this->forge->addKey('fornitore_id');
        $this->forge->addKey('giacenza_corrente');
        $this->forge->createTable('mag_articoli');

        $this->db->query('ALTER TABLE mag_articoli ADD CONSTRAINT fk_magart_categoria
            FOREIGN KEY (categoria_id) REFERENCES mag_categorie(id) ON DELETE SET NULL');
        $this->db->query('ALTER TABLE mag_articoli ADD CONSTRAINT fk_magart_fornitore
            FOREIGN KEY (fornitore_id) REFERENCES fornitori(id) ON DELETE SET NULL');
        $this->db->query('ALTER TABLE mag_articoli ADD CONSTRAINT fk_magart_posizione
            FOREIGN KEY (posizione_id) REFERENCES mag_posizioni(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE mag_articoli DROP FOREIGN KEY fk_magart_categoria');
        $this->db->query('ALTER TABLE mag_articoli DROP FOREIGN KEY fk_magart_fornitore');
        $this->db->query('ALTER TABLE mag_articoli DROP FOREIGN KEY fk_magart_posizione');
        $this->forge->dropTable('mag_articoli');
    }
}
