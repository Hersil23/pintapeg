<?php
/**
 * PintaPeg - Configuracion de base de datos y constantes globales
 */

// Prevenir acceso directo
if (!defined('PINTAPEG')) {
    http_response_code(403);
    exit('Acceso denegado');
}

// =============================================
// Configuracion de base de datos
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'pintapeg');
define('DB_USER', 'root');        // Cambiar en produccion
define('DB_PASS', '');            // Cambiar en produccion
define('DB_CHARSET', 'utf8mb4');

// =============================================
// Conexion PDO
// =============================================
function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(['error' => 'Error de conexion a la base de datos']));
        }
    }

    return $pdo;
}

// =============================================
// Constantes del proyecto
// =============================================
define('UPLOAD_DIR', __DIR__ . '/../uploads/productos/');
define('UPLOAD_MAX_WIDTH', 800);
define('UPLOAD_QUALITY', 80);
define('WHATSAPP_DEFAULT', '04265196026');

// =============================================
// Cabeceras JSON para API
// =============================================
function jsonHeaders(): void {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
}

// =============================================
// Respuesta JSON estandarizada
// =============================================
function jsonResponse(mixed $data, int $code = 200): void {
    http_response_code($code);
    jsonHeaders();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// =============================================
// Validar metodo HTTP
// =============================================
function requireMethod(string $method): void {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        jsonResponse(['error' => 'Metodo no permitido'], 405);
    }
}

// =============================================
// Sanitizar entrada
// =============================================
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// =============================================
// Generar slug
// =============================================
function generateSlug(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[áàäâã]/u', 'a', $text);
    $text = preg_replace('/[éèëê]/u', 'e', $text);
    $text = preg_replace('/[íìïî]/u', 'i', $text);
    $text = preg_replace('/[óòöôõ]/u', 'o', $text);
    $text = preg_replace('/[úùüû]/u', 'u', $text);
    $text = preg_replace('/ñ/u', 'n', $text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}
