<table cellpadding="0" cellspacing="0" border="0"
       style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#374151;max-width:380px;background-color:#ffffff !important;border-top:3px solid #388fc0;">
    <tr>
        <td style="padding:12px 16px 14px 16px;background-color:#ffffff !important;color:#374151 !important;">

            <?php if ($logoSrc): ?>
            <img src="<?= $logoSrc ?>" alt="<?= esc($aziendaNome) ?>"
                 style="max-height:48px;max-width:180px;display:block;margin-bottom:4px;">
            <?php else: ?>
            <div style="font-size:15px;font-weight:bold;color:#388fc0;margin-bottom:4px;">
                <?= esc($aziendaNome) ?>
            </div>
            <?php endif; ?>

            <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">
                Trattamento acqua
            </div>

            <?php if ($indirizzo): ?>
            <div style="font-size:12px;color:#6b7280;margin-bottom:10px;">
                <span style="color:#9ca3af;font-size:11px;">Ind.</span> <?= esc($indirizzo) ?>
            </div>
            <?php endif; ?>

            <table cellpadding="0" cellspacing="0" border="0">
                <?php if ($telefono): ?>
                <tr>
                    <td style="padding:2px 10px 2px 0;color:#9ca3af;font-size:11px;width:14px;">Tel.</td>
                    <td style="padding:2px 0;">
                        <a href="tel:<?= esc($telefono) ?>"
                           style="color:#374151;text-decoration:none;font-size:13px;">
                            <?= esc($telefono) ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($sito): ?>
                <tr>
                    <td style="padding:2px 10px 2px 0;color:#9ca3af;font-size:11px;width:14px;">Web</td>
                    <td style="padding:2px 0;">
                        <?php $url = str_starts_with($sito, 'http') ? $sito : 'https://' . $sito; ?>
                        <a href="<?= esc($url) ?>"
                           style="color:#388fc0;text-decoration:none;font-size:13px;">
                            <?= esc($sito) ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            </table>

        </td>
    </tr>
</table>
