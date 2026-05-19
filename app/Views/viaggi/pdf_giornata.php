<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; padding: 20px 26px; }

/* Header */
table.header { width: 100%; border-bottom: 2px solid #2980b9; margin-bottom: 16px; padding-bottom: 10px; }
.company-name { font-size: 14px; font-weight: bold; color: #1f2937; }
.company-info { color: #6b7280; font-size: 9px; margin-top: 2px; }
.doc-title    { font-size: 16px; font-weight: bold; color: #2980b9; text-align: right; }
.doc-date     { font-size: 11px; font-weight: bold; color: #1f2937; text-align: right; margin-top: 2px; }
.doc-subtitle { font-size: 9px; color: #6b7280; text-align: right; margin-top: 2px; }

/* Tecnico block */
.tecnico-block { margin-bottom: 18px; page-break-inside: avoid; }

.tecnico-header {
    background: #2980b9;
    color: #fff;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: bold;
    border-radius: 3px 3px 0 0;
}
.tecnico-header .veicolo {
    font-size: 9px;
    font-weight: normal;
    opacity: .85;
    margin-top: 2px;
}

/* Tabella tappe */
table.tappe {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #d1d5db;
    border-top: none;
    border-radius: 0 0 3px 3px;
}
table.tappe thead tr {
    background: #ebf5fb;
}
table.tappe thead th {
    padding: 5px 7px;
    text-align: left;
    font-size: 9px;
    color: #374151;
    font-weight: bold;
    border-bottom: 1px solid #d1d5db;
}
table.tappe tbody td {
    padding: 5px 7px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
    font-size: 10px;
}
table.tappe tbody tr:last-child td { border-bottom: none; }
table.tappe tbody tr:nth-child(even) { background: #f9fafb; }

.num  { width: 22px; text-align: center; color: #9ca3af; font-size: 9px; }
.ora  { width: 42px; color: #2980b9; font-weight: bold; white-space: nowrap; }
.tipo { width: 90px; color: #6b7280; font-size: 9px; }
.pri  { width: 55px; }

/* Badge priorità */
.badge { display: inline; padding: 1px 5px; border-radius: 2px; font-size: 9px; font-weight: bold; }
.urgente    { background: #fee2e2; color: #991b1b; }
.ordinario  { background: #e5e7eb; color: #374151; }
.programmato{ background: #dbeafe; color: #1e40af; }

/* Nessun viaggio */
.empty { text-align: center; color: #9ca3af; padding: 30px; font-size: 11px; }

/* Footer */
.footer {
    border-top: 1px solid #e5e7eb;
    margin-top: 20px;
    padding-top: 6px;
    text-align: center;
    font-size: 8px;
    color: #9ca3af;
}
</style>
</head>
<body>

<!-- Intestazione -->
<table class="header"><tr>
    <td style="width:55%;">
        <?php if ($azienda['logo']): ?>
            <img src="<?= $azienda['logo'] ?>" alt="<?= esc($azienda['nome']) ?>"
                 style="max-height:40px;max-width:160px;margin-bottom:3px;display:block;">
        <?php else: ?>
            <div class="company-name"><?= esc($azienda['nome']) ?></div>
        <?php endif; ?>
        <div class="company-info">
            <?php
                $riga = array_filter([
                    $azienda['indirizzo'],
                    trim(($azienda['cap'] ?? '') . ' ' . ($azienda['citta'] ?? '')),
                    $azienda['telefono'],
                ]);
                echo esc(implode(' — ', $riga));
            ?>
        </div>
    </td>
    <td style="width:45%; vertical-align:top;">
        <div class="doc-title">Riepilogo Giornaliero</div>
        <div class="doc-date"><?= data_ita($data) ?></div>
        <div class="doc-subtitle">
            <?= count($viaggi) === 1 ? '1 viaggio approvato' : count($viaggi) . ' viaggi approvati' ?>
            &nbsp;|&nbsp; Stampa: <?= date('d/m/Y H:i') ?>
        </div>
    </td>
</tr></table>

<?php if (empty($viaggi)): ?>
    <div class="empty">Nessun viaggio approvato per questa data.</div>
<?php else: ?>

<?php foreach ($viaggi as $viaggio): ?>
<div class="tecnico-block">

    <div class="tecnico-header">
        <?= esc($viaggio['cognome'] . ' ' . $viaggio['nome']) ?>
        <div class="veicolo">
            <?php if ($viaggio['veicolo_nome']): ?>
                &#9656; <?= esc($viaggio['veicolo_nome'] . ($viaggio['veicolo_targa'] ? ' — ' . $viaggio['veicolo_targa'] : '')) ?>
            <?php else: ?>
                &#9656; Nessun veicolo assegnato
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($viaggio['tappe'])): ?>
        <table class="tappe"><tbody>
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:10px;">Nessuna tappa</td></tr>
        </tbody></table>
    <?php else: ?>
    <table class="tappe">
        <thead>
            <tr>
                <th class="num">#</th>
                <th>Cliente / Luogo</th>
                <th class="tipo">Tipo intervento</th>
                <th class="pri">Priorità</th>
                <th class="ora">Arrivo est.</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($viaggio['tappe'] as $t):
            $nomeCliente = $t['cliente_tipo'] === 'persona_fisica'
                ? trim(($t['cliente_cognome'] ?? '') . ' ' . ($t['cliente_nome'] ?? ''))
                : ($t['ragsoc'] ?? '—');
            $luogo = array_filter([$t['luogo_intervento'], $t['citta'] ?: $t['cliente_citta']]);
            $pr    = $priorita[$t['priorita']] ?? ['label' => $t['priorita'], 'badge' => 'badge-secondary'];
        ?>
            <tr>
                <td class="num"><?= $t['ordine'] ?></td>
                <td>
                    <strong><?= esc($nomeCliente) ?></strong>
                    <?php if ($luogo): ?>
                        <br><span style="color:#6b7280;font-size:9px;"><?= esc(implode(' — ', $luogo)) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($t['descrizione'])): ?>
                        <br><span style="color:#9ca3af;font-size:9px;font-style:italic;"><?= esc(mb_strimwidth($t['descrizione'], 0, 100, '…')) ?></span>
                    <?php endif; ?>
                </td>
                <td class="tipo"><?= esc($t['tipo_nome'] ?: $t['tipo_intervento']) ?></td>
                <td class="pri">
                    <span class="badge <?= esc($t['priorita']) ?>"><?= esc($pr['label']) ?></span>
                </td>
                <td class="ora">
                    <?= $t['ora_arrivo_stimata'] ? substr($t['ora_arrivo_stimata'], 0, 5) : '—' ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</div>
<?php endforeach; ?>
<?php endif; ?>

<div class="footer">
    <?= esc($azienda['nome']) ?> — Documento generato automaticamente il <?= date('d/m/Y \a\l\l\e H:i') ?>
</div>

</body>
</html>
