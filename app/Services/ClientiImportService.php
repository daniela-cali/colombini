<?php

namespace App\Services;

class ClientiImportService
{
    // Campi destinazione disponibili per il mapping
    public const CAMPI_DEST = [
        'codice'      => 'Codice *',
        'tipo'        => 'Tipo (societa / persona_fisica)',
        'ragsoc'      => 'Ragione Sociale',
        'cognome'     => 'Cognome',
        'nome'        => 'Nome',
        'piva'        => 'P.IVA',
        'cfisc'       => 'Codice Fiscale',
        'indirizzo'   => 'Indirizzo',
        'citta'       => 'Città',
        'cap'         => 'CAP',
        'provincia'   => 'Provincia',
        'nazione'     => 'Nazione',
        'telefono'    => 'Telefono',
        'email'       => 'Email',
        'note'        => 'Note',
        'id_external' => 'ID Esterno',
        'stato'       => 'Stato (1=attivo, 0=disattivo)',
    ];

    public function leggiHeaders(string $filePath): array
    {
        $sep    = $this->detectSeparator($filePath);
        $handle = fopen($filePath, 'r');
        if (! $handle) throw new \RuntimeException('Impossibile aprire il file.');

        $row = fgetcsv($handle, 0, $sep);
        fclose($handle);

        if (! $row) return [];

        return array_map('trim', $this->convertEncoding($row));
    }

    public function import(string $filePath, array $mapping): array
    {
        $sep    = $this->detectSeparator($filePath);
        $handle = fopen($filePath, 'r');
        if (! $handle) throw new \RuntimeException('Impossibile aprire il file.');

        // Salta header
        fgetcsv($handle, 0, $sep);

        $records = [];
        $saltati = 0;

        while (($row = fgetcsv($handle, 0, $sep)) !== false) {
            $row = $this->convertEncoding($row);

            $record = [];
            foreach ($mapping as $csvCol => $dbField) {
                if ($dbField === '' || $dbField === null) continue;
                $idx = (int) $csvCol;
                $record[$dbField] = trim($row[$idx] ?? '');
            }

            // codice obbligatorio
            if (empty($record['codice'])) {
                $saltati++;
                continue;
            }

            // normalizza tipo
            $record['tipo'] = $this->normalizeTipo($record['tipo'] ?? '');

            // se persona fisica e il nome è arrivato nel campo ragsoc (es. "ROSSI MARIO"),
            // splitta sul primo spazio: prima parte → cognome, resto → nome
            if ($record['tipo'] === 'persona_fisica' && ! empty($record['ragsoc'])) {
                $parti = explode(' ', trim($record['ragsoc']), 2);
                $record['cognome'] = $parti[0] ?? null;
                $record['nome']    = isset($parti[1]) ? trim($parti[1]) : null;
                unset($record['ragsoc']);
            }

            // cast stato
            if (isset($record['stato'])) {
                $record['stato'] = filter_var($record['stato'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            } else {
                $record['stato'] = 1;
            }

            $record['dt_import'] = date('Y-m-d');

            // converti stringhe vuote in null
            foreach ($record as $k => $v) {
                if ($v === '') $record[$k] = null;
            }

            if (empty($record['nazione'])) {
                $record['nazione'] = 'NON VALORIZZATO';
            }
            // codice non può essere null
            if (empty($record['codice'])) { $saltati++; continue; }

            $records[] = $record;
        }

        fclose($handle);

        // deduplica per codice: in caso di righe doppiate nel CSV vince l'ultima
        $unici = [];
        foreach ($records as $r) {
            $unici[(string) $r['codice']] = $r;
        }
        $records = array_values($unici);

        if (empty($records)) {
            return ['inseriti' => 0, 'aggiornati' => 0, 'saltati' => $saltati, 'totale' => 0];
        }

        $model     = new \App\Models\ClienteModel();
        $inseriti  = 0;
        $aggiornati = 0;

        foreach ($records as $record) {
            $existing = $model->where('codice', $record['codice'])->first();
            if ($existing) {
                $model->update($existing['id'], $record);
                $aggiornati++;
            } else {
                $model->insert($record);
                $inseriti++;
            }
        }

        return [
            'totale'     => count($records),
            'inseriti'   => $inseriti,
            'aggiornati' => $aggiornati,
            'saltati'    => $saltati,
        ];
    }

    private function detectSeparator(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) return ';';

        $firstLine = fgets($handle);
        fclose($handle);

        return substr_count((string) $firstLine, ';') >= substr_count((string) $firstLine, ',') ? ';' : ',';
    }

    private function convertEncoding(array $row): array
    {
        return array_map(function ($v) {
            if (! mb_detect_encoding($v, 'UTF-8', true)) {
                $v = mb_convert_encoding($v, 'UTF-8', 'ISO-8859-1');
            }
            return $v;
        }, $row);
    }

    private function normalizeTipo(string $val): string
    {
        $val = strtolower(trim($val));
        $pf  = ['s', 'p', 'pf', 'persona', 'persona_fisica', 'privato', 'privata', 'f', '0'];
        return in_array($val, $pf, true) ? 'persona_fisica' : 'societa';
    }
}
