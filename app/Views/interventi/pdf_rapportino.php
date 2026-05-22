<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; padding: 24px 28px; }

/* Header */
table.header { width: 100%; border-bottom: 2px solid #2980b9; margin-bottom: 18px; padding-bottom: 12px; }
.company-name { font-size: 15px; font-weight: bold; color: #1f2937; }
.company-info { color: #6b7280; font-size: 10px; margin-top: 3px; }
.doc-title { font-size: 17px; font-weight: bold; color: #2980b9; text-align: right; }
.doc-subtitle { font-size: 10px; color: #6b7280; text-align: right; margin-top: 3px; }

/* Sezioni */
h2 { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .05em;
     color: #6b7280; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin: 18px 0 10px; }

/* Tabella dettagli */
table.dettagli { width: 100%; border-collapse: collapse; }
table.dettagli td { padding: 5px 6px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
table.dettagli td.lbl { width: 32%; color: #6b7280; font-size: 10px; font-weight: bold; }
table.dettagli tr:last-child td { border-bottom: none; }

/* Badge stato */
.badge { display: inline; padding: 2px 7px; border-radius: 3px; font-size: 10px; font-weight: bold; }
.s-completato { background:#d1fae5; color:#065f46; }
.s-pianificato { background:#e5e7eb; color:#374151; }
.s-in_corso    { background:#fef3c7; color:#92400e; }
.s-annullato   { background:#fee2e2; color:#991b1b; }

/* Note */
.note-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:3px; padding:10px; font-size:11px; line-height:1.5; }

/* Firme */
table.firme { width: 100%; margin-top: 44px; }
table.firme td { width: 50%; padding: 0 20px; text-align: center; }
.firma-line { border-top: 1px solid #9ca3af; padding-top: 6px; font-size: 10px; color: #6b7280; }
.firma-img { max-width: 180px; max-height: 70px; display: block; margin: 0 auto 6px; }

/* Footer */
.footer { border-top: 1px solid #e5e7eb; margin-top: 28px; padding-top: 7px;
          text-align: center; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>

<!-- Intestazione azienda -->
<table class="header"><tr>
    <td>
        <?php if ($azienda['logo']): ?>
        <img src="<?= $azienda['logo'] ?>" alt="<?= esc($azienda['nome']) ?>"
             style="max-height:44px;max-width:180px;margin-bottom:4px;display:block;">
        <?php else: ?>
        <div class="company-name"><?= esc($azienda['nome'] ?: 'Colombini Piscine') ?></div>
        <?php endif; ?>
        <div class="company-info">
            <?php
                $riga = array_filter([$azienda['indirizzo'], trim(($azienda['cap'] ?? '') . ' ' . ($azienda['citta'] ?? ''))]);
                echo esc(implode(' — ', $riga));
            ?>
        </div>
    </td>
    <td>
        <div class="doc-title">Rapportino di Intervento</div>
        <div class="doc-subtitle">N.&nbsp;<?= $intervento['id'] ?> &nbsp;|&nbsp; <?= date('d/m/Y') ?></div>
    </td>
</tr></table>

<!-- Dati intervento -->
<h2>Dati Intervento</h2>
<table class="dettagli">
    <tr><td class="lbl">Tipo intervento</td><td><?= esc($tipi[$intervento['tipo_intervento']] ?? $intervento['tipo_intervento']) ?></td></tr>
    <tr><td class="lbl">Stato</td>
        <td><span class="badge s-<?= $intervento['stato'] ?>"><?= $stati[$intervento['stato']]['label'] ?? $intervento['stato'] ?></span></td></tr>
    <tr><td class="lbl">Cliente</td><td><?= esc($nomeCliente) ?></td></tr>
    <?php if ($intervento['luogo_intervento']): ?>
    <tr><td class="lbl">Luogo</td><td><?= esc($intervento['luogo_intervento']) ?></td></tr>
    <?php endif; ?>
    <?php if ($intervento['citta']): ?>
    <tr><td class="lbl">Città</td><td><?= esc($intervento['citta']) ?></td></tr>
    <?php endif; ?>
    <tr><td class="lbl">Tecnico</td><td><?= esc($nomeTecnico) ?></td></tr>
    <?php if ($intervento['data_pianificata']): ?>
    <tr><td class="lbl">Data pianificata</td><td><?= date('d/m/Y H:i', strtotime($intervento['data_pianificata'])) ?></td></tr>
    <?php endif; ?>
    <?php if ($intervento['data_completamento']): ?>
    <tr><td class="lbl">Completato il</td><td><?= date('d/m/Y H:i', strtotime($intervento['data_completamento'])) ?></td></tr>
    <?php endif; ?>
    <?php if ($intervento['descrizione']): ?>
    <tr><td class="lbl">Descrizione</td><td><?= nl2br(esc($intervento['descrizione'])) ?></td></tr>
    <?php endif; ?>
</table>

<!-- Note di chiusura -->
<?php if ($intervento['note_chiusura']): ?>
<h2>Note di Chiusura</h2>
<div class="note-box"><?= nl2br(esc($intervento['note_chiusura'])) ?></div>
<?php endif; ?>

<!-- Firme -->
<table class="firme"><tr>
    <td>
        <?php if ($intervento['firma_tecnico']): ?>
            <?php if (str_starts_with($intervento['firma_tecnico'], 'data:image/png;base64,')): ?>
                <img src="<?= $intervento['firma_tecnico'] ?>" alt="Firma tecnico" class="firma-img">
            <?php else: ?>
                <div style="font-size:9px;color:#6b7280;margin-bottom:6px;">✓ Presa visione</div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="firma-line">Firma Tecnico — <?= esc($nomeTecnico) ?></div>
    </td>
    <td>
        <?php if ($intervento['firma_cliente']): ?>
            <?php if (str_starts_with($intervento['firma_cliente'], 'data:image/png;base64,')): ?>
                <img src="<?= $intervento['firma_cliente'] ?>" alt="Firma cliente" class="firma-img">
            <?php else: ?>
                <div style="font-size:9px;color:#6b7280;margin-bottom:6px;">✓ Presa visione</div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="firma-line">Firma Cliente — <?= esc($nomeCliente) ?></div>
    </td>
</tr></table>

<div class="footer">
    Rapportino generato il <?= date('d/m/Y \a\l\l\e H:i') ?>
    <?php if ($azienda['nome']): ?> &mdash; <?= esc($azienda['nome']) ?><?php endif; ?>
</div>

</body>
</html>
