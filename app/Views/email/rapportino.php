<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        :root { color-scheme: light only; }
    </style>
</head>
<body bgcolor="#ffffff" style="margin:0;padding:0;background-color:#ffffff;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"
       style="background-color:#ffffff;">
    <tr>
        <td align="left" style="padding:24px 24px 16px 24px;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#374151;background-color:#ffffff;">
            <p style="margin:0 0 12px 0;">Gentile <?= htmlspecialchars($nomeCliente, ENT_QUOTES, 'UTF-8') ?>,</p>
            <p style="margin:0 0 12px 0;">in allegato il rapportino relativo all'intervento #<?= (int) $interventoId ?>.</p>
            <p style="margin:0 0 24px 0;">Cordiali saluti,</p>
            <?= $firma ?>
        </td>
    </tr>
</table>
</body>
</html>
