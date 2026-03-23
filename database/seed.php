<?php
/**
 * PintaPeg - Script para crear usuario admin inicial
 *
 * Ejecutar UNA sola vez desde terminal:
 * php database/seed.php
 *
 * O desde el navegador (luego eliminar acceso)
 */

define('PINTAPEG', true);
require_once __DIR__ . '/../api/config.php';

$email = 'mpintapeg@gmail.com';
$password = 'PintaPeg2024!'; // CAMBIAR en primer login
$nombre = 'Admin PintaPeg';

try {
    $db = getDB();

    // Verificar si ya existe
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "El usuario admin ya existe.\n";
        exit;
    }

    // Crear usuario
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare('INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nombre, $email, $hash, 'admin']);

    echo "Usuario admin creado exitosamente.\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "IMPORTANTE: Cambiar la contraseña en el primer login.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
