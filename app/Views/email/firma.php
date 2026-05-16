<table cellpadding="0" cellspacing="0" border="0"
       style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#374151;max-width:480px;border:1px solid #e5e7eb;border-radius:4px;background:#ffffff;">
    <tr>

        <!-- Logo -->
        <td style="padding:14px 16px;vertical-align:middle;border-right:2px solid #e5e7eb;width:90px;text-align:center;">
            <?php if ($logoSrc): ?>
            <img src="<?= $logoSrc ?>" alt="<?= esc($aziendaNome) ?>"
                 style="max-width:80px;max-height:64px;display:block;margin:0 auto;">
            <?php else: ?>
            <span style="font-size:18px;font-weight:bold;color:#388fc0;letter-spacing:.02em;">
                <?= esc(mb_substr($aziendaNome, 0, 2)) ?>
            </span>
            <?php endif; ?>
        </td>

        <!-- Info -->
        <td style="padding:14px 18px;vertical-align:middle;">
            <div style="font-size:15px;font-weight:bold;color:#388fc0;margin-bottom:1px;">
                <?= esc($aziendaNome) ?>
            </div>
            <div style="font-size:11px;color:#9ca3af;margin-bottom:10px;text-transform:uppercase;letter-spacing:.06em;">
                Assistenza Piscine
            </div>

            <table cellpadding="0" cellspacing="0" border="0">
                <?php if ($telefono): ?>
                <tr>
                    <td style="padding:2px 8px 2px 0;color:#388fc0;font-size:13px;">&#128222;</td>
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
                    <td style="padding:2px 8px 2px 0;color:#388fc0;font-size:13px;">&#9993;</td>
                    <td style="padding:2px 0;">
                        <?php $url = str_starts_with($sito, 'http') ? $sito : 'https://' . $sito; ?>
                        <a href="<?= esc($url) ?>"
                           style="color:#374151;text-decoration:none;font-size:13px;">
                            <?= esc($sito) ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($indirizzo): ?>
                <tr>
                    <td style="padding:2px 8px 2px 0;color:#388fc0;font-size:13px;">&#128205;</td>
                    <td style="padding:2px 0;font-size:13px;color:#374151;">
                        <?= esc($indirizzo) ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </td>

    </tr>
</table>
