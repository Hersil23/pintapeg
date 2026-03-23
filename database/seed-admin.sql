-- =============================================
-- PintaPeg - Crear usuario admin inicial
-- =============================================
-- Password: PintaPeg2024!
-- Hash generado con password_hash('PintaPeg2024!', PASSWORD_BCRYPT)
-- IMPORTANTE: Cambiar la contraseña despues del primer login
-- =============================================

INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES
  ('Admin PintaPeg', 'mpintapeg@gmail.com',
   '$2y$10$8K1p/a0dR3Yb8tVnPQk1OeGZvAn5jJ2mOhR9N6mGjTV4kGz4W3Km2',
   'admin')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- =============================================
-- NOTA: El hash de arriba es un placeholder.
-- Para generar el hash correcto, ejecuta esto en la terminal
-- de cPanel o en phpMyAdmin > SQL:
--
-- Si tienes acceso a Terminal en cPanel:
--   php -r "echo password_hash('PintaPeg2024!', PASSWORD_BCRYPT);"
--
-- Luego reemplaza el hash en el INSERT de arriba.
--
-- Alternativa: ejecuta database/seed.php desde el navegador
-- una sola vez: https://pintapeg.com/database/seed.php
-- (luego elimina el acceso a /database/)
-- =============================================
