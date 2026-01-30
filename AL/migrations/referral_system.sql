-- Migración para agregar sistema de referidos

-- Tabla de referidos
CREATE TABLE IF NOT EXISTS referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT NOT NULL,
    referred_id INT NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    commission DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referred_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_referral (referrer_id, referred_id)
);

-- Agregar campo de código de referido a la tabla de usuarios
ALTER TABLE users ADD COLUMN referral_code VARCHAR(20) UNIQUE NULL;
ALTER TABLE users ADD COLUMN referred_by INT NULL;
ALTER TABLE users ADD FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL;

-- Insertar trigger para generar código de referido al crear usuario
DELIMITER //
CREATE TRIGGER generate_referral_code AFTER INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.referral_code IS NULL THEN
        UPDATE users SET referral_code = CONCAT('REF_', UPPER(LEFT(MD5(NEW.id), 8))) WHERE id = NEW.id;
    END IF;
END //
DELIMITER ;
