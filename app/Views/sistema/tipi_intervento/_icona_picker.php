<div class="form-group">
    <label>Icona <small class="text-muted font-weight-normal">(Font Awesome)</small></label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas <?= esc($valoreIcona) ?>" id="icona-preview-icon"></i>
            </span>
        </div>
        <input type="text" name="icona" id="icona" class="form-control"
               value="<?= esc($valoreIcona) ?>" placeholder="fa-tools" maxlength="50" readonly
               style="background:#fff; cursor:pointer;" onclick="toggleIconaPicker()">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary" onclick="toggleIconaPicker()">
                <i class="fas fa-th mr-1"></i><span class="d-none d-sm-inline">Scegli</span>
            </button>
        </div>
    </div>

    <div id="icona-picker" class="border rounded p-2 mt-1" style="display:none; background:#fff;">
        <input type="text" id="icona-search" class="form-control form-control-sm mb-2"
               placeholder="Cerca icona… (es. water, tool, home)">
        <div id="icona-grid"
             style="display:flex;flex-wrap:wrap;gap:6px;max-height:220px;overflow-y:auto;"></div>
        <p id="icona-no-results" class="text-muted small mt-2 mb-0" style="display:none;">
            Nessuna icona trovata.
        </p>
    </div>
</div>

<script>
(function () {
    var ICONE = [
        /* acqua / piscine */
        ['fa-swimming-pool','piscina'],['fa-water','acqua'],['fa-tint','goccia'],
        ['fa-shower','doccia'],['fa-bath','vasca'],['fa-faucet','rubinetto'],
        ['fa-snowflake','freddo'],['fa-fire','fuoco'],['fa-temperature-high','temperatura'],
        /* impianti / tecnica */
        ['fa-filter','filtro'],['fa-wind','ventilazione'],['fa-plug','elettrico'],
        ['fa-bolt','corrente'],['fa-wifi','rete'],['fa-satellite-dish','antenna'],
        ['fa-thermometer-half','termostato'],['fa-tachometer-alt','pressione'],
        /* utensili */
        ['fa-tools','attrezzi'],['fa-wrench','chiave'],['fa-hammer','martello'],
        ['fa-screwdriver','cacciavite'],['fa-cog','ingranaggio'],['fa-cogs','ingranaggi'],
        ['fa-toolbox','cassetta'],['fa-hard-hat','sicurezza'],
        /* edifici */
        ['fa-home','casa'],['fa-building','edificio'],['fa-industry','industria'],
        ['fa-store','negozio'],['fa-warehouse','magazzino'],['fa-hotel','hotel'],
        ['fa-school','scuola'],['fa-hospital','ospedale'],
        /* veicoli */
        ['fa-truck','camion'],['fa-car','auto'],['fa-shuttle-van','furgone'],
        /* business */
        ['fa-handshake','commerciale'],['fa-briefcase','lavoro'],
        ['fa-file-invoice','fattura'],['fa-clipboard-list','lista'],
        ['fa-chart-bar','report'],['fa-calendar-alt','calendario'],
        ['fa-clock','orario'],['fa-bell','notifica'],
        /* persone */
        ['fa-user','utente'],['fa-users','gruppo'],['fa-user-cog','tecnico'],
        /* generico */
        ['fa-star','preferito'],['fa-check-circle','completato'],['fa-flag','flag'],
        ['fa-tag','etichetta'],['fa-map-marker-alt','posizione'],['fa-phone','telefono'],
        ['fa-envelope','email'],['fa-info-circle','info'],['fa-exclamation-triangle','avviso'],
        ['fa-leaf','ecologico'],['fa-recycle','riciclo'],['fa-sun','sole'],
    ];

    var grid   = document.getElementById('icona-grid');
    var search = document.getElementById('icona-search');
    var input  = document.getElementById('icona');
    var noRes  = document.getElementById('icona-no-results');

    function renderIcone(list) {
        grid.innerHTML = '';
        list.forEach(function (item) {
            var cls  = item[0];
            var btn  = document.createElement('button');
            btn.type = 'button';
            btn.title = cls;
            btn.className = 'btn btn-sm btn-outline-secondary' + (input.value === cls ? ' active' : '');
            btn.style.cssText = 'width:42px;height:42px;padding:0;font-size:18px;';
            btn.innerHTML = '<i class="fas ' + cls + '"></i>';
            btn.addEventListener('click', function () {
                input.value = cls;
                document.getElementById('icona-preview-icon').className = 'fas ' + cls;
                grid.querySelectorAll('.btn').forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                document.getElementById('icona-picker').style.display = 'none';
            });
            grid.appendChild(btn);
        });
        noRes.style.display = list.length ? 'none' : 'block';
    }

    search.addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        var filtered = q
            ? ICONE.filter(function (i) { return i[0].includes(q) || i[1].includes(q); })
            : ICONE;
        renderIcone(filtered);
    });

    window.toggleIconaPicker = function () {
        var p = document.getElementById('icona-picker');
        var aperto = p.style.display !== 'none';
        p.style.display = aperto ? 'none' : 'block';
        if (!aperto) {
            search.value = '';
            renderIcone(ICONE);
            search.focus();
        }
    };

    /* Chiudi cliccando fuori */
    document.addEventListener('click', function (e) {
        var picker = document.getElementById('icona-picker');
        if (!picker) return;
        if (!picker.contains(e.target) && e.target !== input
            && !e.target.closest('#icona + .input-group-append')) {
            picker.style.display = 'none';
        }
    });
})();
</script>
