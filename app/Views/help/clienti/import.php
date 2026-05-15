<h5><i class="fas fa-file-csv mr-1"></i> Formato del file</h5>
<p>Carica un file <strong>.csv</strong> con separatore <code>;</code> o <code>,</code> e encoding UTF-8 o ISO-8859-1. Non è necessaria una struttura predefinita: nel passo successivo potrai associare ogni colonna al campo corretto.</p>
<h5><i class="fas fa-sync-alt mr-1"></i> Aggiornamento vs. inserimento</h5>
<p>I clienti già presenti con lo stesso <strong>codice</strong> vengono aggiornati, non duplicati. I clienti senza codice corrispondente vengono creati come nuovi.</p>
<h5><i class="fas fa-map-marker-alt mr-1"></i> Geocodifica dopo l'import</h5>
<p>Al termine dell'import gli indirizzi non vengono geocodificati automaticamente. Esegui <code>php spark geocode:clienti</code> da terminale per rilevare le coordinate di tutti i clienti importati.</p>
<p><span class="badge-tip"><i class="fas fa-lightbulb mr-1"></i> Il sistema memorizza il mapping colonne dell'ultimo import: la prossima volta i campi saranno già precompilati.</span></p>
