<table class="table table-sm mb-0">
    <tbody>
    <?php foreach ($righe as $nota): ?>
    <tr>
        <td class="align-middle pl-3">
            <?php if ($nota['cod_articolo']): ?>
                <strong>[<?= esc($nota['cod_articolo']) ?>]</strong> <?= esc($nota['descrizione']) ?>
                <?php if ($nota['quantita']): ?>
                    <span class="badge badge-secondary ml-1"><?= (float) $nota['quantita'] ?></span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($nota['note']): ?>
                <span class="text-muted small<?= $nota['cod_articolo'] ? ' d-block' : '' ?>"><?= esc($nota['note']) ?></span>
            <?php endif; ?>
        </td>
        <td class="align-middle text-right pr-3" style="width:90px;">
            <?php if ($nota['intervento_id']): ?>
                <a href="<?= base_url('interventi/' . $nota['intervento_id']) ?>"
                   class="text-muted small mr-1" title="Intervento #<?= $nota['intervento_id'] ?>">
                    <i class="fas fa-tools"></i>
                </a>
            <?php endif; ?>
            <?php if ($eliminabile): ?>
            <form method="post"
                  action="<?= base_url('clienti/' . $nota['cliente_id'] . '/materiali/' . $nota['id'] . '/elimina') ?>"
                  class="d-inline"
                  onsubmit="return confirm('Eliminare questa voce?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-xs btn-outline-danger" title="Elimina">
                    <i class="fas fa-times"></i>
                </button>
            </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
