<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);

$html = <<<HTML
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; line-height: 1.6; margin: 0; padding: 0; }
    h1 { font-size: 18px; color: #1a3a5c; border-bottom: 2px solid #1a3a5c; padding-bottom: 6px; margin-bottom: 4px; }
    h2 { font-size: 14px; color: #1a5276; margin-top: 20px; margin-bottom: 4px; border-left: 4px solid #1a5276; padding-left: 8px; }
    h3 { font-size: 11px; color: #333; margin-top: 10px; margin-bottom: 2px; }
    p { margin: 4px 0 8px 0; }
    ul { margin: 4px 0 8px 16px; padding: 0; }
    li { margin-bottom: 3px; }
    .subtitle { color: #555; font-size: 12px; margin-bottom: 2px; }
    .date { color: #888; font-size: 10px; margin-bottom: 20px; }
    .phase { background: #f4f8fb; border: 1px solid #cde; border-radius: 4px; padding: 10px 14px; margin-bottom: 14px; }
    .phase-num { display: inline-block; background: #1a5276; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; font-size: 11px; font-weight: bold; margin-right: 6px; }
    .order-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .order-table td { padding: 5px 10px; border: 1px solid #cde; font-size: 10px; }
    .order-table tr:nth-child(odd) td { background: #f4f8fb; }
    .order-table td:first-child { font-weight: bold; color: #1a5276; width: 30px; text-align: center; }
    .footer { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 6px; font-size: 9px; color: #aaa; text-align: center; }
</style>
</head>
<body>

<h1>Sistema VRP — Piano di Lavoro Tecnico</h1>
<p class="subtitle">Colombini S.n.c. — Gestionale Piscine</p>
<p class="date">Documento generato il: 17 maggio 2026 &nbsp;|&nbsp; Versione: 1.0 (bozza)</p>

<h2>Obiettivo</h2>
<p>Implementazione di un sistema di ottimizzazione logistica (Vehicle Routing Problem) per la pianificazione giornaliera degli interventi tecnici sul campo. Il sistema utilizzerà le API di <strong>OpenRouteService (ORS)</strong> con solver Vroom per ottenere rotte ottimali su rete stradale reale.</p>

<h2><span class="phase-num">1</span> Fase 1 — Arricchimento dati (prerequisiti)</h2>
<div class="phase">
<p>Prima di poter ottimizzare le rotte è necessario arricchire il modello dati con nuove relazioni:</p>
<ul>
    <li><strong>Competenze tecnico</strong> — tabella pivot <code>tecnici_competenze</code> (tecnico ↔ tipi_intervento): indica quali tipi di intervento un tecnico è abilitato a svolgere</li>
    <li><strong>Requisiti tecnico/veicolo</strong> — relazione che lega ogni tecnico ai veicoli compatibili con le sue esigenze (es. cambio automatico per tecnico con disabilità)</li>
    <li><strong>Idoneità veicolo</strong> — relazione <code>veicoli_tipi_intervento</code>: quali veicoli sono adatti a quali tipi di intervento (es. furgone attrezzato per certi lavori)</li>
</ul>
</div>

<h2><span class="phase-num">2</span> Fase 2 — Controllo geocodifica</h2>
<div class="phase">
<p>Prima di lanciare l'ottimizzazione il sistema deve garantire che tutti i clienti degli interventi abbiano coordinate lat/lng valide:</p>
<ul>
    <li>Controllo pre-ottimizzazione con lista degli interventi "bloccanti" (cliente non geocodificato)</li>
    <li>Utility di geocodifica singola e massiva (utile anche per import CSV da DB esterno)</li>
    <li>Stato geocodifica visibile nell'anagrafica cliente</li>
</ul>
</div>

<h2><span class="phase-num">3</span> Fase 3 — Motore di pre-assegnazione</h2>
<div class="phase">
<p>Prima della chiamata ORS il sistema costruisce la proposta di assegnazione tecnico/veicolo:</p>
<ul>
    <li>Per ogni intervento: filtra i tecnici per competenza sul tipo di intervento</li>
    <li>Incrocia con disponibilità veicoli compatibili (requisiti tecnico + idoneità veicolo)</li>
    <li>Produce una proposta che l'admin può rivedere prima di ottimizzare</li>
    <li>Gestisce i casi limite: nessun tecnico idoneo, nessun veicolo disponibile</li>
</ul>
</div>

<h2><span class="phase-num">4</span> Fase 4 — Interfaccia Pianificazione</h2>
<div class="phase">
<p>Nuova sezione nel cruscotto (sotto Calendario), progettata per essere richiamabile anche dal Calendario in futuro.</p>
<h3>Passo 1 — Setup giornata</h3>
<ul>
    <li>Selettore data</li>
    <li>Elenco interventi pianificati con indicatori: geocodifica OK/KO, tecnico assegnato/da assegnare, veicolo proposto</li>
    <li>Risoluzione manuale dei casi problematici prima di procedere</li>
</ul>
<h3>Passo 2 — Ottimizzazione e risultato</h3>
<ul>
    <li>Chiamata ORS con veicoli (tecnici + orari da configurazione sede) e job (interventi + durata + coordinate)</li>
    <li>Punto di partenza e ritorno: sede aziendale (<code>settings.azienda.sede_lat/lng</code>) — rotta chiusa</li>
    <li>Mappa Leaflet con polilinee colorate per tecnico e marker numerati per ordine di visita</li>
    <li>Riepilogo testuale: tecnico → sequenza interventi con orari stimati e km totali</li>
    <li>Possibilità di aggiustamento manuale prima della conferma</li>
</ul>
</div>

<h2><span class="phase-num">5</span> Fase 5 — Salvataggio viaggi e autorizzazione</h2>
<div class="phase">
<p>Alla conferma del piano ottimizzato:</p>
<ul>
    <li>Creazione record in tabella <code>viaggi</code> — una riga per tecnico/giornata con le tappe ordinate</li>
    <li>Tabella <code>viaggi_tappe</code> — una riga per intervento nella rotta: ordine, orario stimato, km dalla tappa precedente</li>
    <li>Sezione <strong>Assistenza → Viaggi</strong>: lista viaggi per tecnico, filtrabili per data e stato</li>
    <li>Workflow stato: <em>bozza</em> → <em>confermato</em> (admin) → revisione se necessario</li>
    <li>Vista dettaglio viaggio con mappa e riepilogo tappe</li>
</ul>
</div>

<h2>Ordine di esecuzione</h2>
<table class="order-table">
    <tr><td>1</td><td><strong>Competenze/requisiti tecnici e veicoli</strong> — dati foundation per le regole di assegnazione</td></tr>
    <tr><td>2</td><td><strong>Controllo e utility geocodifica</strong> — garanzia qualità dati prima di tutto</td></tr>
    <tr><td>3</td><td><strong>Sezione Viaggi + tabelle DB</strong> — struttura che riceverà i risultati</td></tr>
    <tr><td>4</td><td><strong>Interfaccia Pianificazione — passo 1</strong> — setup e validazione giornata</td></tr>
    <tr><td>5</td><td><strong>Integrazione ORS + mappa risultato</strong> — cuore del sistema</td></tr>
    <tr><td>6</td><td><strong>Workflow autorizzazione admin</strong> — gestione post-ottimizzazione</td></tr>
</table>

<h2>Stack tecnologico</h2>
<ul>
    <li><strong>Solver VRP:</strong> OpenRouteService API — endpoint <code>/optimization</code> (Vroom solver, gratuito fino a 2000 req/giorno)</li>
    <li><strong>Mappe:</strong> Leaflet.js (già presente nel progetto)</li>
    <li><strong>Backend:</strong> CodeIgniter 4 — pattern MVC esistente</li>
    <li><strong>Geocodifica:</strong> Nominatim / OpenStreetMap (già utilizzato per clienti e sede)</li>
</ul>

<div class="footer">
    Colombini S.n.c. — Documento interno — Generato automaticamente dal gestionale
</div>

</body>
</html>
HTML;

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$output = $dompdf->output();
$path = __DIR__ . '/VRP_Piano_Lavoro.pdf';
file_put_contents($path, $output);
echo "PDF generato: " . $path . PHP_EOL;
