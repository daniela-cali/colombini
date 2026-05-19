# Checklist messa in produzione — Colombini SNC

Da completare prima del go-live sulla VPS. Spunta ogni punto dopo averlo verificato.

---

## 1. File `.env` sul server

- [ ] `CI_ENVIRONMENT = production` (non `development`)
- [ ] `app.baseURL = 'https://colombini-snc.it'` (con HTTPS, senza slash finale)
- [ ] `database.default.username` — **non usare `root`**, creare un utente dedicato (vedi sezione DB)
- [ ] `database.default.password` — password robusta (≥ 20 caratteri, caratteri speciali)
- [ ] `encryption.key` — generare una chiave con `php spark key:generate` e incollarla qui
- [ ] `email.SMTPUser` / `email.SMTPPass` — aggiornare con le credenziali di produzione (`@colombini-snc.it`)
- [ ] `ORS_API_KEY` — verificare che la chiave sia valida e non scaduta
- [ ] Il file `.env` **non è nella root pubblica** (`public/`) ma nella root del progetto — verificare
- [ ] I permessi del file: `chmod 600 .env` (leggibile solo dall'utente web)

---

## 2. Database MariaDB

- [ ] Creare un utente dedicato (non root) con accesso solo al db dell'applicazione:
  ```sql
  CREATE USER 'colombini_app'@'localhost' IDENTIFIED BY 'PasswordRobusta!';
  GRANT SELECT, INSERT, UPDATE, DELETE ON colombini.* TO 'colombini_app'@'localhost';
  FLUSH PRIVILEGES;
  ```
- [ ] Verificare che `root` non abbia accesso remoto:
  ```sql
  SELECT host, user FROM mysql.user WHERE user = 'root';
  -- deve mostrare solo 'localhost', non '%'
  ```
- [ ] Porta MySQL/MariaDB (3306) **non esposta** su IP pubblico — verificare con `netstat -tlnp | grep 3306` (deve mostrare `127.0.0.1:3306`, non `0.0.0.0:3306`)
- [ ] Backup automatico del database configurato (cron giornaliero con `mysqldump`)

---

## 3. File system e permessi

- [ ] Cartella `writable/` scrivibile dal web server, non dagli altri:
  ```bash
  chmod -R 775 writable/
  chown -R www-data:www-data writable/
  ```
- [ ] Cartella `public/uploads/` stessa cosa:
  ```bash
  chmod -R 775 public/uploads/
  chown -R www-data:www-data public/uploads/
  ```
- [ ] Le cartelle fuori da `public/` (`app/`, `system/`, `.env`, `composer.json`) **non accessibili via browser** — il document root del virtualhost deve puntare a `public/`, non alla root del progetto
- [ ] Verificare che `public/uploads/` non permetta l'esecuzione di PHP (configurazione Nginx/Apache — vedi sezione web server)
- [ ] Rimuovere file non necessari dalla root: `demo_clienti.csv`, `demo_interventi.sql`, `demo_interventi_2.sql` (dati di test)

---

## 4. Web server (Nginx o Apache)

### Se Nginx
- [ ] `document_root` punta a `/var/www/colombini/public`
- [ ] Blocco esecuzione PHP in uploads:
  ```nginx
  location ~* /uploads/.*\.php$ {
      deny all;
  }
  ```
- [ ] Header di sicurezza nel virtualhost:
  ```nginx
  add_header X-Frame-Options "SAMEORIGIN";
  add_header X-Content-Type-Options "nosniff";
  add_header X-XSS-Protection "1; mode=block";
  add_header Referrer-Policy "strict-origin-when-cross-origin";
  ```
- [ ] Directory listing disabilitato: `autoindex off;`

### Se Apache
- [ ] `DocumentRoot` punta a `/var/www/colombini/public`
- [ ] `.htaccess` presente in `public/` (CI4 lo include di default)
- [ ] `Options -Indexes` nel virtualhost (disabilita directory listing)
- [ ] Blocco esecuzione PHP in uploads:
  ```apache
  <Directory /var/www/colombini/public/uploads>
      php_flag engine off
  </Directory>
  ```

---

## 5. HTTPS / SSL

- [ ] Certificato SSL installato (Let's Encrypt con Certbot è gratuito):
  ```bash
  certbot --nginx -d colombini-snc.it -d www.colombini-snc.it
  ```
- [ ] Redirect automatico HTTP → HTTPS configurato
- [ ] Verificare scadenza automatica del certificato: `certbot renew --dry-run`
- [ ] In `.env`: `app.forceGlobalSecureRequests = true` (forza HTTPS anche internamente a CI4)

---

## 6. CodeIgniter 4 — configurazione produzione

- [ ] `CI_ENVIRONMENT = production` — disabilita le pagine di errore dettagliate (Whoops)
- [ ] Verificare `app/Config/App.php`: `$indexPage = ''` (già impostato)
- [ ] Verificare che i log di errore vadano in `writable/logs/` e **non** siano accessibili via browser
- [ ] `writable/cache/` e `writable/session/` hanno i permessi corretti (vedi sezione file system)
- [ ] Eseguire `php spark migrate` sul server dopo ogni deploy

---

## 7. Email

- [ ] Configurare un indirizzo `@colombini-snc.it` come mittente (es. `noreply@colombini-snc.it`)
- [ ] Testare l'invio email dal pannello impostazioni dopo il deploy
- [ ] Verificare che il record SPF del dominio includa il server SMTP usato (evita che le email finiscano in spam)

---

## 8. Accesso SSH alla VPS

- [ ] Accesso SSH solo con chiave (disabilitare login con password): in `/etc/ssh/sshd_config` → `PasswordAuthentication no`
- [ ] Cambiare porta SSH default (22) se possibile
- [ ] Firewall (ufw o iptables): aprire solo le porte necessarie (80, 443, SSH)
  ```bash
  ufw allow 80/tcp
  ufw allow 443/tcp
  ufw allow <porta-ssh>/tcp
  ufw enable
  ```

---

## 9. Verifica finale

- [ ] Aprire `https://colombini-snc.it` in incognito e verificare che non compaiano errori CI4
- [ ] Tentare di accedere a `https://colombini-snc.it/.env` — deve rispondere 403 o 404
- [ ] Tentare di accedere a `https://colombini-snc.it/app/` — deve rispondere 403 o 404
- [ ] Testare login, creazione intervento, pianificazione, stampa PDF
- [ ] Verificare che il logo aziendale sia presente in `public/uploads/`
- [ ] Controllare `writable/logs/` che non ci siano errori dopo i primi accessi

---

> Quando sei pronta per il go-live, puoi condividere l'accesso SSH e verifico tutto insieme a te punto per punto.
