<?php
/**
 * PintaPeg API - Registro y consulta de ventas
 *
 * POST /api/ventas.php                  → Registrar venta (publico, desde checkout)
 * GET  /api/ventas.php                  → Listar ventas (admin)
 * GET  /api/ventas.php?id=1             → Detalle de venta (admin)
 * GET  /api/ventas.php?action=stats     → Metricas de ventas (admin)
 */

define('PINTAPEG', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth-helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        requireAuth();
        if ($action === 'stats') {
            getEstadisticas();
        } elseif (!empty($_GET['id'])) {
            getVenta((int)$_GET['id']);
        } else {
            listarVentas();
        }
        break;
    case 'POST':
        registrarVenta();
        break;
    default:
        jsonResponse(['error' => 'Metodo no permitido'], 405);
}

// =============================================
// Registrar venta (desde checkout WhatsApp)
// =============================================
function registrarVenta(): void {
    $input = json_decode(file_get_contents('php://input'), true);

    $productosJson = $input['productos'] ?? [];
    $totalUsd = (float)($input['total_usd'] ?? 0);
    $totalVes = (float)($input['total_ves'] ?? 0);
    $tasaUsada = (float)($input['tasa_usada'] ?? 0);
    $monedaCliente = ($input['moneda_cliente'] ?? 'usd') === 'ves' ? 'ves' : 'usd';
    $nombreCliente = sanitize($input['nombre_cliente'] ?? '');
    $direccion = sanitize($input['direccion'] ?? '');
    $referenciaEntrega = sanitize($input['referencia_entrega'] ?? '');

    // Validaciones
    $errores = [];
    if (empty($productosJson)) $errores[] = 'Los productos son requeridos';
    if ($totalUsd <= 0) $errores[] = 'El total USD es invalido';
    if (empty($nombreCliente)) $errores[] = 'El nombre del cliente es requerido';
    if (empty($direccion)) $errores[] = 'La direccion es requerida';

    if (!empty($errores)) {
        jsonResponse(['error' => implode('. ', $errores)], 400);
    }

    // Generar referencia unica
    $referencia = 'PP-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    $db = getDB();
    $stmt = $db->prepare(
        'INSERT INTO ventas (referencia, productos_json, total_usd, total_ves, tasa_usada, moneda_cliente, nombre_cliente, direccion, referencia_entrega, fecha)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
    );
    $stmt->execute([
        $referencia,
        json_encode($productosJson, JSON_UNESCAPED_UNICODE),
        $totalUsd,
        $totalVes,
        $tasaUsada,
        $monedaCliente,
        $nombreCliente,
        $direccion,
        $referenciaEntrega ?: null,
    ]);

    jsonResponse([
        'success' => true,
        'referencia' => $referencia,
        'id' => (int)$db->lastInsertId(),
    ], 201);
}

// =============================================
// Listar ventas (admin)
// =============================================
function listarVentas(): void {
    $db = getDB();

    $where = [];
    $params = [];

    // Filtrar por fecha
    if (!empty($_GET['desde'])) {
        $where[] = 'fecha >= ?';
        $params[] = sanitize($_GET['desde']) . ' 00:00:00';
    }
    if (!empty($_GET['hasta'])) {
        $where[] = 'fecha <= ?';
        $params[] = sanitize($_GET['hasta']) . ' 23:59:59';
    }

    $whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    $limit = min((int)($_GET['limit'] ?? 50), 200);
    $offset = max((int)($_GET['offset'] ?? 0), 0);

    $sql = "SELECT id, referencia, total_usd, total_ves, tasa_usada, moneda_cliente,
                   nombre_cliente, fecha, created_at
            FROM ventas
            $whereSQL
            ORDER BY fecha DESC
            LIMIT $limit OFFSET $offset";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $ventas = $stmt->fetchAll();

    // Total de registros
    $countSQL = "SELECT COUNT(*) as total FROM ventas $whereSQL";
    $countStmt = $db->prepare($countSQL);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];

    jsonResponse([
        'ventas' => $ventas,
        'total' => (int)$total,
        'limit' => $limit,
        'offset' => $offset,
    ]);
}

// =============================================
// Detalle de venta (admin)
// =============================================
function getVenta(int $id): void {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM ventas WHERE id = ?');
    $stmt->execute([$id]);
    $venta = $stmt->fetch();

    if (!$venta) {
        jsonResponse(['error' => 'Venta no encontrada'], 404);
    }

    // Decodificar productos JSON
    $venta['productos'] = json_decode($venta['productos_json'], true);

    jsonResponse($venta);
}

// =============================================
// Estadisticas de ventas (admin)
// =============================================
function getEstadisticas(): void {
    $db = getDB();

    // Ventas de hoy
    $stmt = $db->query("SELECT COUNT(*) as total, COALESCE(SUM(total_usd), 0) as monto_usd
                         FROM ventas WHERE DATE(fecha) = CURDATE()");
    $hoy = $stmt->fetch();

    // Ventas del mes
    $stmt = $db->query("SELECT COUNT(*) as total, COALESCE(SUM(total_usd), 0) as monto_usd
                         FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
    $mes = $stmt->fetch();

    // Ventas totales
    $stmt = $db->query("SELECT COUNT(*) as total, COALESCE(SUM(total_usd), 0) as monto_usd FROM ventas");
    $total = $stmt->fetch();

    // Ventas por dia (ultimos 30 dias)
    $stmt = $db->query(
        "SELECT DATE(fecha) as dia, COUNT(*) as ventas, SUM(total_usd) as monto_usd
         FROM ventas
         WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
         GROUP BY DATE(fecha)
         ORDER BY dia ASC"
    );
    $porDia = $stmt->fetchAll();

    jsonResponse([
        'hoy' => [
            'ventas' => (int)$hoy['total'],
            'monto_usd' => (float)$hoy['monto_usd'],
        ],
        'mes' => [
            'ventas' => (int)$mes['total'],
            'monto_usd' => (float)$mes['monto_usd'],
        ],
        'total' => [
            'ventas' => (int)$total['total'],
            'monto_usd' => (float)$total['monto_usd'],
        ],
        'por_dia' => $porDia,
    ]);
}
