-- Integrated users migration for the Asteria + produits database.
-- Safe to run once in phpMyAdmin; the PHP Database class also checks these columns automatically.

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    secret_code VARCHAR(255) NULL,
    reset_token VARCHAR(255) NULL,
    reset_expires_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user';
ALTER TABLE users ADD COLUMN secret_code VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN reset_expires_at DATETIME NULL;

ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id;
ALTER TABLE orders ADD CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- If one of the ALTER TABLE lines says "Duplicate column", that column already exists.
-- Then designate at least one administrator:
-- UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';
