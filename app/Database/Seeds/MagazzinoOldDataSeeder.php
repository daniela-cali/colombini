<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\CLI\CLI;

// Importa fornitori e articoli dal vecchio gestionale magazzino (magColombini.sql).
// Esecuzione: php spark db:seed MagazzinoOldDataSeeder
// Idempotente: non fa nulla se mag_articoli contiene già dati.
class MagazzinoOldDataSeeder extends Seeder
{
    // Mappa cod_fornitore vecchio → categoria_id nuova.
    // 1=Grundfos, 3=Emec, 14=Culligan → Ricambi addolcitori (id=2)
    // 2=Fluidra, 4=Europool, 13=Newpool → Ricambi piscine (id=1)
    private const CATEGORIA_MAP = [
        1  => 2,
        3  => 2,
        14 => 2,
        2  => 1,
        4  => 1,
        13 => 1,
    ];

    public function run()
    {
        $db = \Config\Database::connect();

        // Controllo idempotenza
        if ($db->table('mag_articoli')->countAllResults() > 0) {
            CLI::write('Articoli già presenti — seeder saltato.', 'yellow');
            return;
        }

        $sqlFile = ROOTPATH . 'doc/magColombini/magColombini.sql';

        if (! file_exists($sqlFile)) {
            CLI::write('File SQL non trovato: ' . $sqlFile, 'red');
            return;
        }

        CLI::write('Lettura SQL...', 'cyan');
        $sql = str_replace("\r\n", "\n", file_get_contents($sqlFile));

        // ── Tabelle temporanee ────────────────────────────────────────────────
        $db->query('DROP TABLE IF EXISTS `_tmp_for_old`, `_tmp_art_old`');

        $db->query("CREATE TABLE `_tmp_for_old` (
            `cod_fornitore` int UNSIGNED NOT NULL,
            `des_fornitore` varchar(100) CHARACTER SET utf8mb4,
            `fg_inattivo`   tinyint NULL
        ) ENGINE=InnoDB");

        $db->query("CREATE TABLE `_tmp_art_old` (
            `cod_articolo`   varchar(20)    NOT NULL,
            `des_articolo`   varchar(500)   CHARACTER SET utf8mb4,
            `cd_unimis`      char(3),
            `cod_posizione`  tinyint        UNSIGNED NOT NULL DEFAULT 0,
            `coordinate`     varchar(10),
            `cod_fornitore`  int            UNSIGNED NOT NULL DEFAULT 0,
            `scorta_min`     smallint       UNSIGNED NOT NULL DEFAULT 0,
            `lotto_riordino` smallint       UNSIGNED NOT NULL DEFAULT 0,
            `esistenza`      smallint       NOT NULL DEFAULT 0,
            `prezzo_acquisto` decimal(8,2)  NOT NULL DEFAULT 0.00,
            `prezzo_vendita`  decimal(7,2)  NOT NULL DEFAULT 0.00,
            `perc_sconto_1`   decimal(5,2)  NOT NULL DEFAULT 0.00,
            `perc_sconto_2`   decimal(5,2)  NOT NULL DEFAULT 0.00,
            `perc_sconto_3`   decimal(5,2)  NOT NULL DEFAULT 0.00
        ) ENGINE=InnoDB");

        // ── Estrae e carica fornitori ─────────────────────────────────────────
        CLI::write('Carico fornitori...', 'cyan');
        $forInsert = $this->extractInsert($sql, 't_fornitori');
        if ($forInsert) {
            $db->query(str_replace('`t_fornitori`', '`_tmp_for_old`', $forInsert));
        } else {
            CLI::write('Blocco INSERT t_fornitori non trovato.', 'red');
            $db->query('DROP TABLE IF EXISTS `_tmp_for_old`, `_tmp_art_old`');
            return;
        }

        // ── Estrae e carica articoli (phpMyAdmin li spezza in più blocchi) ──────
        CLI::write('Carico articoli...', 'cyan');
        $artBlocks = $this->extractAllInserts($sql, 't_articoli');
        if (empty($artBlocks)) {
            CLI::write('Blocchi INSERT t_articoli non trovati.', 'red');
            $db->query('DROP TABLE IF EXISTS `_tmp_for_old`, `_tmp_art_old`');
            return;
        }
        foreach ($artBlocks as $block) {
            $db->query(str_replace('`t_articoli`', '`_tmp_art_old`', $block));
        }
        CLI::write('  → ' . count($artBlocks) . ' blocchi INSERT eseguiti.', 'cyan');

        // ── Migra fornitori — inserimento uno per uno per tracciare gli ID ──────
        CLI::write('Migro fornitori → fornitori...', 'cyan');
        $forRows = $db->query(
            "SELECT cod_fornitore, des_fornitore FROM `_tmp_for_old` WHERE cod_fornitore > 0 ORDER BY cod_fornitore"
        )->getResultArray();

        $fornitoreMap = []; // old cod_fornitore => new fornitori.id
        $now = date('Y-m-d H:i:s');
        foreach ($forRows as $row) {
            $db->table('fornitori')->insert([
                'ragione_sociale' => $row['des_fornitore'],
                'attivo'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            $fornitoreMap[(int) $row['cod_fornitore']] = $db->insertID();
        }
        $nFor = count($fornitoreMap);

        // ── Migra articoli — CASE costruito con ID reali (evita collation mismatch) ─
        CLI::write('Migro articoli → mag_articoli...', 'cyan');

        // CASE fornitore_id: usa gli ID reali appena inseriti
        $caseFor = 'CASE a.cod_fornitore';
        foreach ($fornitoreMap as $oldCod => $newId) {
            $caseFor .= " WHEN {$oldCod} THEN {$newId}";
        }
        $caseFor .= ' ELSE NULL END';

        // CASE categoria_id: basato sul cod_fornitore vecchio
        $caseCat = 'CASE a.cod_fornitore';
        foreach (self::CATEGORIA_MAP as $oldCod => $catId) {
            $caseCat .= " WHEN {$oldCod} THEN {$catId}";
        }
        $caseCat .= ' ELSE NULL END';

        $db->query("INSERT INTO `mag_articoli`
            (cod_articolo, descrizione, unita_misura, posizione_id, coordinate,
             fornitore_id, scorta_min, lotto_riordino, giacenza_corrente,
             prezzo_acquisto, perc_sconto_1, perc_sconto_2, perc_sconto_3,
             prezzo_vendita, categoria_id, created_at, updated_at)
            SELECT
                TRIM(a.cod_articolo),
                TRIM(a.des_articolo),
                COALESCE(NULLIF(TRIM(a.cd_unimis), ''), 'N'),
                NULLIF(a.cod_posizione, 0),
                NULLIF(TRIM(a.coordinate), ''),
                {$caseFor},
                a.scorta_min,
                a.lotto_riordino,
                a.esistenza,
                a.prezzo_acquisto,
                NULLIF(a.perc_sconto_1, 0),
                NULLIF(a.perc_sconto_2, 0),
                NULLIF(a.perc_sconto_3, 0),
                a.prezzo_vendita,
                {$caseCat},
                '{$now}', '{$now}'
            FROM `_tmp_art_old` a");

        $nArt = $db->affectedRows();

        // ── Pulizia ───────────────────────────────────────────────────────────
        $db->query('DROP TABLE IF EXISTS `_tmp_for_old`, `_tmp_art_old`');

        CLI::write("✓ Importati {$nFor} fornitori e {$nArt} articoli.", 'green');
    }

    // Estrae il primo blocco INSERT INTO `$table` ... ; dal dump SQL.
    private function extractInsert(string $sql, string $table): ?string
    {
        $blocks = $this->extractAllInserts($sql, $table);
        return $blocks[0] ?? null;
    }

    // Estrae tutti i blocchi INSERT INTO `$table` — phpMyAdmin li può spezzare in più chunk.
    private function extractAllInserts(string $sql, string $table): array
    {
        $needle  = "INSERT INTO `{$table}`";
        $blocks  = [];
        $from    = 0;

        while (($start = strpos($sql, $needle, $from)) !== false) {
            // Ogni blocco termina con ";\n" — troviamo la fine del blocco corrente
            $end = strpos($sql, ";\n", $start);
            if ($end === false) {
                break;
            }
            $blocks[] = substr($sql, $start, $end - $start + 2); // include ";\n"
            $from = $end + 2;
        }

        return $blocks;
    }
}
