-- ============================================================
-- Script anonimizzazione dati per ambiente demo
-- Eseguire sul DB LOCALE prima di esportare per la demo
-- ============================================================

-- Clienti: nomi, indirizzi, contatti
UPDATE clienti SET
    nome        = CONCAT('Nome', id),
    cognome     = CONCAT('Cognome', id),
    ragsoc      = CASE WHEN tipo = 'societa' THEN CONCAT('Azienda Demo Srl ', id) ELSE NULL END,
    piva        = CASE WHEN piva IS NOT NULL THEN CONCAT('0', LPAD(id, 10, '0')) ELSE NULL END,
    cfisc       = CASE WHEN cfisc IS NOT NULL THEN CONCAT('DMODMO', LPAD(id, 2, '0'), 'A01H501A') ELSE NULL END,
    indirizzo   = CONCAT('Via Demo ', id),
    telefono    = CASE WHEN telefono IS NOT NULL THEN CONCAT('333', LPAD(id, 7, '0')) ELSE NULL END,
    email       = CASE WHEN email IS NOT NULL THEN CONCAT('cliente', id, '@demo.example.com') ELSE NULL END,
    note        = NULL
WHERE id not IN(1,2);


-- Utenti app e portale: nomi e username
UPDATE users SET
    nome     = CONCAT('Nome', id),
    cognome  = CONCAT('Cognome', id),
    username = CONCAT('utente', id),
    telefono = CASE WHEN telefono IS NOT NULL THEN CONCAT('333', LPAD(id, 7, '0')) ELSE NULL END
WHERE ruolo != 'admin';

-- Email di accesso (tabella Shield)
UPDATE auth_identities SET
    secret  = CONCAT('utente', user_id, '@demo.example.com')
WHERE type = 'email_password'
  AND user_id NOT IN (SELECT id FROM users WHERE ruolo = 'admin');

-- Note interne interventi
UPDATE interventi SET
    descrizione  = CASE WHEN descrizione IS NOT NULL THEN 'Descrizione intervento demo.' ELSE NULL END,
    note_interne = NULL;

-- Testo richieste portale
UPDATE richieste SET
    descrizione = 'Richiesta di assistenza demo.'
WHERE descrizione IS NOT NULL;

-- Verifica risultato
SELECT 'Clienti anonimizzati:' AS info, COUNT(*) AS n FROM clienti
UNION ALL
SELECT 'Utenti anonimizzati:', COUNT(*) FROM users WHERE ruolo != 'admin'
UNION ALL
SELECT 'Interventi anonimizzati:', COUNT(*) FROM interventi;
