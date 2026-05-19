<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; padding: 22px 28px; }

/* Header */
table.header { width: 100%; border-bottom: 2px solid #2980b9; margin-bottom: 16px; padding-bottom: 10px; }
.company-name { font-size: 14px; font-weight: bold; color: #1f2937; }
.company-info { color: #6b7280; font-size: 9px; margin-top: 2px; }
.doc-title    { font-size: 15px; font-weight: bold; color: #2980b9; text-align: right; }
.doc-tecnico  { font-size: 12px; font-weight: bold; color: #1f2937; text-align: right; margin-top: 3px; }
.doc-subtitle { font-size: 9px; color: #6b7280; text-align: right; margin-top: 2px; }

/* Info viaggio */
table.info-viaggio { width: 100%; border-collapse: collapse; margin-bottom: 16px; background: #ebf5fb; border: 1px solid #bfdbfe; border-radius: 3px; }
table.info-viaggio td { padding: 6px 10px; font-size: 10px; }
table.info-viaggio .lbl { color: #6b7280; font-size: 9px; font-weight: bold; width: 90px; }

/* Tabella tappe */
table.tappe { width: 100%; border-collapse: collapse; }
table.tappe thead tr { background: #2980b9; color: #fff; }
table.tappe thead th { padding: 6px 8px; text-align: left; font-size: 9px; font-weight: bold; }
table.tappe tbody td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
table.tappe tbody tr:last-child td { border-bottom: 2px solid #d1d5db; }
table.tappe tbody tr:nth-child(even) { background: #f9fafb; }

.num  { width: 24px; text-align: center; font-size: 12px; font-weight: bold; color: #2980b9; }
.ora  { width: 48px; text-align: center; color: #2980b9; font-weight: bold; font-size: 11px; }
.pri  { width: 60px; }
.tipo { width: 85px; color: #6b7280; font-size: 9px; }

/* Badge priorità */
.badge { display: inline; padding: 2px 6px; border-radius: 2px; font-size: 9px; font-weight: bold; }
.urgente     { background: #fee2e2; color: #991b1b; }
.ordinario   { background: #e5e7eb; color: #374151; }
.programmato { background: #dbeafe; color: #1e40af; }

/* Note tappa */
.note-tappa { color: #9ca3af; font-size: 9px; font-style: italic; margin-top: 2px; }

/* Footer */
.footer { border-top: 1px solid #e5e7eb; margin-top: 20px; padding-top: 6px;
          text-align: center; font-size: 8px; color: #9ca3af; }
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
        <div class="doc-title">Foglio di Viaggio</div>
        <div class="doc-tecnico"><?= esc($viaggio['cognome'] . ' ' . $viaggio['nome']) ?></div>
        <div class="doc-subtitle"><?= data_ita($viaggio['data']) ?> &nbsp;|&nbsp; Stampa: <?= date('d/m/Y H:i') ?></div>
    </td>
</tr></table>

<!-- Info viaggio -->
<table class="info-viaggio"><tr>
    <td><span class="lbl">Data</span> <?= data_ita($viaggio['data']) ?></td>
    <td>
        <span class="lbl">Veicolo</span>
        <?= $viaggio['veicolo_nome']
            ? esc($viaggio['veicolo_nome'] . ($viaggio['veicolo_targa'] ? ' — ' . $viaggio['veicolo_targa'] : ''))
            : '<em style="color:#9ca3af;">Non assegnato</em>' ?>
    </td>
    <td><span class="lbl">Tappe</span> <?= count($viaggio['tappe']) ?></td>
</tr></table>

<!-- Tappe -->
<?php if (empty($viaggio['tappe'])): ?>
    <p style="text-align:center;color:#9ca3af;padding:20px 0;">Nessuna tappa per questo viaggio.</p>
<?php else: ?>
<table class="tappe">
    <thead>
        <tr>
            <th class="num">#</th>
            <th class="ora">Arrivo</th>
            <th>Cliente / Luogo</th>
            <th class="tipo">Tipo</th>
            <th class="pri">Priorità</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($viaggio['tappe'] as $t):
        $nomeCliente = ($t['cliente_tipo'] ?? '') === 'persona_fisica'
            ? trim(($t['cliente_cognome'] ?? '') . ' ' . ($t['cliente_nome'] ?? ''))
            : ($t['ragsoc'] ?? '—');
        $luogo = array_filter([$t['luogo_intervento'], $t['citta'] ?: $t['cliente_citta']]);
        $pr    = $priorita[$t['priorita']] ?? ['label' => $t['priorita'], 'badge' => 'badge-secondary'];
    ?>
        <tr>
            <td class="num"><?= $t['ordine'] ?></td>
            <td class="ora"><?= $t['ora_arrivo_stimata'] ? substr($t['ora_arrivo_stimata'], 0, 5) : '—' ?></td>
            <td>
                <strong><?= esc($nomeCliente) ?></strong>
                <?php if ($luogo): ?>
                    <br><span style="color:#6b7280;font-size:9px;"><?= esc(implode(' — ', $luogo)) ?></span>
                <?php endif; ?>
                <?php if (!empty($t['descrizione'])): ?>
                    <div class="note-tappa"><?= esc(mb_strimwidth($t['descrizione'], 0, 120, '…')) ?></div>
                <?php endif; ?>
            </td>
            <td class="tipo"><?= esc($t['tipo_nome'] ?: $t['tipo_intervento']) ?></td>
            <td class="pri"><span class="badge <?= esc($t['priorita']) ?>"><?= esc($pr['label']) ?></span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<div class="footer">
    <?= esc($azienda['nome']) ?> — Documento generato automaticamente il <?= date('d/m/Y \a\l\l\e H:i') ?>
</div>

</body>
</html>
