<?php
/**
 * PintaPeg API - Autenticacion y sesiones
 *
 * POST /api/auth.php?action=login   → Iniciar sesion
 * POST /api/auth.php?action=logout  → Cerrar sesion
 * GET  /api/auth.php?action=check   → Verificar sesion activa
 */

define('PINTAPEG', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth-helper.php';

initSession();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check':
        handleCheck();
        break;
    default:
        jsonResponse(['error' => 'Accion no valida'], 400);
}

// =============================================
// Login
// =============================================
function handleLogin(): void {
    requireMethod('POST');

    $input = json_decode(file_get_contents('php://input'), true);
    $email = sanitize($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if (empty($email) || empty($password)) {
        jsonResponse(['error' => 'Email y contraseña son requeridos'], 400);
    }

    $db = getDB();
    $stmt = $db->prepare('SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        jsonResponse(['error' => 'Credenciales incorrectas'], 401);
    }

    // Regenerar ID de sesion para prevenir fixation
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nombre'] = $user['nombre'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_rol'] = $user['rol'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    jsonResponse([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'nombre' => $user['nombre'],
            'email' => $user['email'],
            'rol' => $user['rol'],
        ],
        'csrf_token' => $_SESSION['csrf_token'],
    ]);
}

// =============================================
// Logout
// =============================================
function handleLogout(): void {
    requireMethod('POST');
    session_destroy();
    jsonResponse(['success' => true]);
}

// =============================================
// Check sesion
// =============================================
function handleCheck(): void {
    requireMethod('GET');

    if (empty($_SESSION['user_id'])) {
        jsonResponse(['authenticated' => false], 401);
    }

    jsonResponse([
        'authenticated' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_nombre'],
            'email' => $_SESSION['user_email'],
            'rol' => $_SESSION['user_rol'],
        ],
        'csrf_token' => $_SESSION['csrf_token'],
    ]);
}
