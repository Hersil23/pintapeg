<?php
/**
 * PintaPeg API - CRUD Categorias
 *
 * GET    /api/categorias.php              → Listar todas (publico)
 * GET    /api/categorias.php?id=1         → Obtener una
 * POST   /api/categorias.php              → Crear (admin)
 * POST   /api/categorias.php?action=update → Actualizar (admin)
 * POST   /api/categorias.php?action=delete → Eliminar (admin)
 */

define('PINTAPEG', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth-helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if (!empty($_GET['id'])) {
            getCategoria((int)$_GET['id']);
        } else {
            listarCategorias();
        }
        break;
    case 'POST':
        requireAuth();
        requireCSRF();
        switch ($action) {
            case 'update':
                actualizarCategoria();
                break;
            case 'delete':
                eliminarCategoria();
                break;
            default:
                crearCategoria();
        }
        break;
    default:
        jsonResponse(['error' => 'Metodo no permitido'], 405);
}

// =============================================
// Listar categorias (publico)
// =============================================
function listarCategorias(): void {
    $db = getDB();
    $soloActivas = empty($_GET['all']) ? 'WHERE activo = 1' : '';
    $stmt = $db->query("SELECT id, nombre, slug, imagen, activo, orden FROM categorias $soloActivas ORDER BY orden ASC, nombre ASC");
    jsonResponse($stmt->fetchAll());
}

// =============================================
// Obtener una categoria
// =============================================
function getCategoria(int $id): void {
    $db = getDB();
    $stmt = $db->prepare('SELECT id, nombre, slug, imagen, activo, orden FROM categorias WHERE id = ?');
    $stmt->execute([$id]);
    $cat = $stmt->fetch();

    if (!$cat) {
        jsonResponse(['error' => 'Categoria no encontrada'], 404);
    }

    jsonResponse($cat);
}

// =============================================
// Crear categoria
// =============================================
function crearCategoria(): void {
    $input = json_decode(file_get_contents('php://input'), true);

    $nombre = sanitize($input['nombre'] ?? '');
    $activo = isset($input['activo']) ? (int)$input['activo'] : 1;
    $orden = isset($input['orden']) ? (int)$input['orden'] : 0;

    if (empty($nombre)) {
        jsonResponse(['error' => 'El nombre es requerido'], 400);
    }

    $slug = generateSlug($nombre);
    $db = getDB();

    // Verificar slug unico
    $slug = uniqueSlug($db, 'categorias', $slug);

    $stmt = $db->prepare('INSERT INTO categorias (nombre, slug, activo, orden) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nombre, $slug, $activo, $orden]);

    jsonResponse([
        'success' => true,
        'id' => (int)$db->lastInsertId(),
        'slug' => $slug,
    ], 201);
}

// =============================================
// Actualizar categoria
// =============================================
function actualizarCategoria(): void {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = (int)($input['id'] ?? 0);
    if ($id <= 0) {
        jsonResponse(['error' => 'ID invalido'], 400);
    }

    $db = getDB();
    $campos = [];
    $valores = [];

    if (isset($input['nombre'])) {
        $nombre = sanitize($input['nombre']);
        if (empty($nombre)) {
            jsonResponse(['error' => 'El nombre no puede estar vacio'], 400);
        }
        $campos[] = 'nombre = ?';
        $valores[] = $nombre;
        $slug = uniqueSlug($db, 'categorias', generateSlug($nombre), $id);
        $campos[] = 'slug = ?';
        $valores[] = $slug;
    }

    if (isset($input['imagen'])) {
        $campos[] = 'imagen = ?';
        $valores[] = sanitize($input['imagen']);
    }

    if (isset($input['activo'])) {
        $campos[] = 'activo = ?';
        $valores[] = (int)$input['activo'];
    }

    if (isset($input['orden'])) {
        $campos[] = 'orden = ?';
        $valores[] = (int)$input['orden'];
    }

    if (empty($campos)) {
        jsonResponse(['error' => 'No hay campos para actualizar'], 400);
    }

    $valores[] = $id;
    $sql = 'UPDATE categorias SET ' . implode(', ', $campos) . ' WHERE id = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute($valores);

    jsonResponse(['success' => true]);
}

// =============================================
// Eliminar categoria
// =============================================
function eliminarCategoria(): void {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = (int)($input['id'] ?? 0);

    if ($id <= 0) {
        jsonResponse(['error' => 'ID invalido'], 400);
    }

    $db = getDB();

    // Verificar que no tenga productos
    $stmt = $db->prepare('SELECT COUNT(*) as total FROM productos WHERE categoria_id = ?');
    $stmt->execute([$id]);
    $count = $stmt->fetch()['total'];

    if ($count > 0) {
        jsonResponse(['error' => "No se puede eliminar: tiene $count producto(s) asociado(s)"], 409);
    }

    $stmt = $db->prepare('DELETE FROM categorias WHERE id = ?');
    $stmt->execute([$id]);

    jsonResponse(['success' => true]);
}

// =============================================
// Helper: Slug unico
// =============================================
function uniqueSlug(PDO $db, string $table, string $slug, ?int $excludeId = null): string {
    $original = $slug;
    $counter = 1;

    while (true) {
        $sql = "SELECT id FROM $table WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = $original . '-' . $counter;
        $counter++;
    }
}
