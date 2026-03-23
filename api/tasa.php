<?php
/**
 * PintaPeg API - Tasa de cambio (dolarapi.com)
 *
 * GET  /api/tasa.php                → Obtener tasas actuales (publico)
 * POST /api/tasa.php?action=update  → Forzar actualizacion (admin)
 */

define('PINTAPEG', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth-helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        obtenerTasas();
        break;
    case 'POST':
        requireAuth();
        requireCSRF();
        if ($action === 'update') {
            actualizarTasas();
        } else {
            jsonResponse(['error' => 'Accion no valida'], 400);
        }
        break;
    default:
        jsonResponse(['error' => 'Metodo no permitido'], 405);
}

// =============================================
// Obtener tasas actuales de la BD
// =============================================
function obtenerTasas(): void {
    $db = getDB();

    // Obtener tasas
    $stmt = $db->query('SELECT tipo, valor, fecha_actualizacion FROM tasa_dolar');
    $tasas = [];
    while ($row = $stmt->fetch()) {
        $tasas[$row['tipo']] = [
            'valor' => (float)$row['valor'],
            'fecha' => $row['fecha_actualizacion'],
        ];
    }

    // Obtener tasa activa desde config
    $stmt = $db->prepare('SELECT valor FROM config_tienda WHERE clave = ?');
    $stmt->execute(['tasa_activa']);
    $config = $stmt->fetch();
    $tasaActiva = $config ? $config['valor'] : 'bcv';

    // Verificar si necesita actualizacion (mas de 24h)
    $necesitaActualizacion = false;
    if (!empty($tasas['bcv'])) {
        $ultimaActualizacion = strtotime($tasas['bcv']['fecha']);
        $necesitaActualizacion = (time() - $ultimaActualizacion) > 86400;
    }

    // Auto-actualizar si es necesario
    if ($necesitaActualizacion || empty($tasas['bcv']) || $tasas['bcv']['valor'] <= 0) {
        fetchTasasDesdeAPI();
        // Re-leer
        $stmt = $db->query('SELECT tipo, valor, fecha_actualizacion FROM tasa_dolar');
        $tasas = [];
        while ($row = $stmt->fetch()) {
            $tasas[$row['tipo']] = [
                'valor' => (float)$row['valor'],
                'fecha' => $row['fecha_actualizacion'],
            ];
        }
    }

    jsonResponse([
        'tasas' => $tasas,
        'tasa_activa' => $tasaActiva,
        'valor_activo' => $tasas[$tasaActiva]['valor'] ?? 0,
    ]);
}

// =============================================
// Forzar actualizacion desde dolarapi.com
// =============================================
function actualizarTasas(): void {
    $resultado = fetchTasasDesdeAPI();
    jsonResponse($resultado);
}

// =============================================
// Fetch desde dolarapi.com
// =============================================
function fetchTasasDesdeAPI(): array {
    $url = 'https://ve.dolarapi.com/v1/dolares';

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 10,
            'header' => 'User-Agent: PintaPeg/1.0',
        ],
    ]);

    $response = @file_get_contents($url, false, $ctx);

    if ($response === false) {
        return ['error' => 'No se pudo conectar a dolarapi.com'];
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        return ['error' => 'Respuesta invalida de dolarapi.com'];
    }

    $db = getDB();
    $resultado = ['success' => true, 'tasas' => []];

    foreach ($data as $item) {
        $nombre = strtolower($item['nombre'] ?? '');
        $promedio = (float)($item['promedio'] ?? 0);

        if ($promedio <= 0) continue;

        if (strpos($nombre, 'oficial') !== false || strpos($nombre, 'bcv') !== false) {
            $stmt = $db->prepare('UPDATE tasa_dolar SET valor = ?, fecha_actualizacion = NOW() WHERE tipo = ?');
            $stmt->execute([$promedio, 'bcv']);
            $resultado['tasas']['bcv'] = $promedio;
        } elseif (strpos($nombre, 'paralelo') !== false) {
            $stmt = $db->prepare('UPDATE tasa_dolar SET valor = ?, fecha_actualizacion = NOW() WHERE tipo = ?');
            $stmt->execute([$promedio, 'paralelo']);
            $resultado['tasas']['paralelo'] = $promedio;
        }
    }

    return $resultado;
}
