-- Exécuter une fois dans phpMyAdmin (base gestion_users) ou en ligne de commande MySQL.
ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user';

-- Ensuite, désigne au moins un compte administrateur (remplace l’email) :
-- UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';
