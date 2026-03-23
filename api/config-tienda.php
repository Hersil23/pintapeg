<?php
/**
 * PintaPeg API - Configuracion general de la tienda
 *
 * GET  /api/config-tienda.php         → Obtener configuracion (publico)
 * POST /api/config-tienda.php         → Actualizar configuracion (admin)
 */

define('PINTAPEG', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth-helper.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        obtenerConfig();
        break;
    case 'POST':
        requireAuth();
        requireCSRF();
        actualizarConfig();
        break;
    default:
        jsonResponse(['error' => 'Metodo no permitido'], 405);
}

// =============================================
// Obtener configuracion
// =============================================
function obtenerConfig(): void {
    $db = getDB();
    $stmt = $db->query('SELECT clave, valor FROM config_tienda');
    $config = [];
    while ($row = $stmt->fetch()) {
        $config[$row['clave']] = $row['valor'];
    }
    jsonResponse($config);
}

// =============================================
// Actualizar configuracion
// =============================================
function actualizarConfig(): void {
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input) || !is_array($input)) {
        jsonResponse(['error' => 'Datos invalidos'], 400);
    }

    // Claves permitidas
    $clavesPermitidas = ['tasa_activa', 'moneda_default', 'whatsapp'];

    // Validaciones especificas
    $validaciones = [
        'tasa_activa' => ['bcv', 'paralelo'],
        'moneda_default' => ['usd', 'ves'],
    ];

    $db = getDB();
    $actualizados = [];

    foreach ($input as $clave => $valor) {
        $clave = sanitize($clave);
        $valor = sanitize($valor);

        if (!in_array($clave, $clavesPermitidas)) {
            continue;
        }

        // Validar valores permitidos
        if (isset($validaciones[$clave]) && !in_array($valor, $validaciones[$clave])) {
            jsonResponse(['error' => "Valor invalido para '$clave'"], 400);
        }

        // Validar WhatsApp (solo numeros, 11 digitos)
        if ($clave === 'whatsapp') {
            $valor = preg_replace('/[^0-9]/', '', $valor);
            if (strlen($valor) < 10 || strlen($valor) > 15) {
                jsonResponse(['error' => 'Numero de WhatsApp invalido'], 400);
            }
        }

        $stmt = $db->prepare('INSERT INTO config_tienda (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = ?');
        $stmt->execute([$clave, $valor, $valor]);
        $actualizados[$clave] = $valor;
    }

    jsonResponse([
        'success' => true,
        'actualizados' => $actualizados,
    ]);
}
