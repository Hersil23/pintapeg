<?php
/**
 * PintaPeg API - CRUD Productos
 *
 * GET    /api/productos.php                    → Listar todos (publico)
 * GET    /api/productos.php?id=1               → Obtener uno
 * GET    /api/productos.php?slug=mdf-15mm      → Obtener por slug
 * GET    /api/productos.php?categoria=1        → Filtrar por categoria
 * GET    /api/productos.php?destacados=1       → Solo destacados
 * POST   /api/productos.php                    → Crear (admin)
 * POST   /api/productos.php?action=update      → Actualizar (admin)
 * POST   /api/productos.php?action=delete      → Eliminar (admin)
 */

define('PINTAPEG', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth-helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if (!empty($_GET['id'])) {
            getProducto((int)$_GET['id']);
        } elseif (!empty($_GET['slug'])) {
            getProductoPorSlug($_GET['slug']);
        } else {
            listarProductos();
        }
        break;
    case 'POST':
        requireAuth();
        requireCSRF();
        switch ($action) {
            case 'update':
                actualizarProducto();
                break;
            case 'delete':
                eliminarProducto();
                break;
            default:
                crearProducto();
        }
        break;
    default:
        jsonResponse(['error' => 'Metodo no permitido'], 405);
}

// =============================================
// Listar productos (publico)
// =============================================
function listarProductos(): void {
    $db = getDB();

    $where = [];
    $params = [];

    // Solo activos para publico
    if (empty($_GET['all'])) {
        $where[] = 'p.activo = 1';
    }

    // Filtrar por categoria
    if (!empty($_GET['categoria'])) {
        $where[] = 'p.categoria_id = ?';
        $params[] = (int)$_GET['categoria'];
    }

    // Solo destacados
    if (!empty($_GET['destacados'])) {
        $where[] = 'p.destacado = 1';
    }

    // Busqueda
    if (!empty($_GET['buscar'])) {
        $where[] = '(p.nombre LIKE ? OR p.descripcion LIKE ?)';
        $term = '%' . sanitize($_GET['buscar']) . '%';
        $params[] = $term;
        $params[] = $term;
    }

    $whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "SELECT p.id, p.categoria_id, p.nombre, p.slug, p.descripcion,
                   p.precio, p.moneda_base, p.stock, p.imagen, p.activo,
                   p.destacado, p.created_at, c.nombre AS categoria_nombre
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            $whereSQL
            ORDER BY p.destacado DESC, p.nombre ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    jsonResponse($stmt->fetchAll());
}

// =============================================
// Obtener producto por ID
// =============================================
function getProducto(int $id): void {
    $db = getDB();
    $stmt = $db->prepare(
        "SELECT p.*, c.nombre AS categoria_nombre
         FROM productos p
         LEFT JOIN categorias c ON p.categoria_id = c.id
         WHERE p.id = ?"
    );
    $stmt->execute([$id]);
    $prod = $stmt->fetch();

    if (!$prod) {
        jsonResponse(['error' => 'Producto no encontrado'], 404);
    }

    jsonResponse($prod);
}

// =============================================
// Obtener producto por slug (para pagina detalle)
// =============================================
function getProductoPorSlug(string $slug): void {
    $slug = sanitize($slug);
    $db = getDB();
    $stmt = $db->prepare(
        "SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
         FROM productos p
         LEFT JOIN categorias c ON p.categoria_id = c.id
         WHERE p.slug = ? AND p.activo = 1"
    );
    $stmt->execute([$slug]);
    $prod = $stmt->fetch();

    if (!$prod) {
        jsonResponse(['error' => 'Producto no encontrado'], 404);
    }

    jsonResponse($prod);
}

// =============================================
// Crear producto
// =============================================
function crearProducto(): void {
    $input = json_decode(file_get_contents('php://input'), true);

    $nombre = sanitize($input['nombre'] ?? '');
    $categoriaId = (int)($input['categoria_id'] ?? 0);
    $descripcion = sanitize($input['descripcion'] ?? '');
    $precio = (float)($input['precio'] ?? 0);
    $monedaBase = ($input['moneda_base'] ?? 'usd') === 'ves' ? 'ves' : 'usd';
    $stock = (int)($input['stock'] ?? 0);
    $activo = isset($input['activo']) ? (int)$input['activo'] : 1;
    $destacado = isset($input['destacado']) ? (int)$input['destacado'] : 0;

    // Validaciones
    $errores = [];
    if (empty($nombre)) $errores[] = 'El nombre es requerido';
    if ($categoriaId <= 0) $errores[] = 'La categoria es requerida';
    if ($precio <= 0) $errores[] = 'El precio debe ser mayor a 0';

    if (!empty($errores)) {
        jsonResponse(['error' => implode('. ', $errores)], 400);
    }

    $slug = generateSlug($nombre);
    $db = getDB();

    // Verificar que la categoria existe
    $stmt = $db->prepare('SELECT id FROM categorias WHERE id = ?');
    $stmt->execute([$categoriaId]);
    if (!$stmt->fetch()) {
        jsonResponse(['error' => 'La categoria no existe'], 400);
    }

    $slug = uniqueSlugProducto($db, $slug);

    $stmt = $db->prepare(
        'INSERT INTO productos (categoria_id, nombre, slug, descripcion, precio, moneda_base, stock, activo, destacado)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$categoriaId, $nombre, $slug, $descripcion, $precio, $monedaBase, $stock, $activo, $destacado]);

    jsonResponse([
        'success' => true,
        'id' => (int)$db->lastInsertId(),
        'slug' => $slug,
    ], 201);
}

// =============================================
// Actualizar producto
// =============================================
function actualizarProducto(): void {
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
        $slug = uniqueSlugProducto($db, generateSlug($nombre), $id);
        $campos[] = 'slug = ?';
        $valores[] = $slug;
    }

    if (isset($input['categoria_id'])) {
        $catId = (int)$input['categoria_id'];
        $stmt = $db->prepare('SELECT id FROM categorias WHERE id = ?');
        $stmt->execute([$catId]);
        if (!$stmt->fetch()) {
            jsonResponse(['error' => 'La categoria no existe'], 400);
        }
        $campos[] = 'categoria_id = ?';
        $valores[] = $catId;
    }

    if (isset($input['descripcion'])) {
        $campos[] = 'descripcion = ?';
        $valores[] = sanitize($input['descripcion']);
    }

    if (isset($input['precio'])) {
        $precio = (float)$input['precio'];
        if ($precio <= 0) {
            jsonResponse(['error' => 'El precio debe ser mayor a 0'], 400);
        }
        $campos[] = 'precio = ?';
        $valores[] = $precio;
    }

    if (isset($input['moneda_base'])) {
        $campos[] = 'moneda_base = ?';
        $valores[] = $input['moneda_base'] === 'ves' ? 'ves' : 'usd';
    }

    if (isset($input['stock'])) {
        $campos[] = 'stock = ?';
        $valores[] = (int)$input['stock'];
    }

    if (isset($input['imagen'])) {
        $campos[] = 'imagen = ?';
        $valores[] = sanitize($input['imagen']);
    }

    if (isset($input['activo'])) {
        $campos[] = 'activo = ?';
        $valores[] = (int)$input['activo'];
    }

    if (isset($input['destacado'])) {
        $campos[] = 'destacado = ?';
        $valores[] = (int)$input['destacado'];
    }

    if (empty($campos)) {
        jsonResponse(['error' => 'No hay campos para actualizar'], 400);
    }

    $valores[] = $id;
    $sql = 'UPDATE productos SET ' . implode(', ', $campos) . ' WHERE id = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute($valores);

    jsonResponse(['success' => true]);
}

// =============================================
// Eliminar producto
// =============================================
function eliminarProducto(): void {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = (int)($input['id'] ?? 0);

    if ($id <= 0) {
        jsonResponse(['error' => 'ID invalido'], 400);
    }

    $db = getDB();

    // Obtener imagen para eliminar archivo
    $stmt = $db->prepare('SELECT imagen FROM productos WHERE id = ?');
    $stmt->execute([$id]);
    $prod = $stmt->fetch();

    if ($prod && $prod['imagen']) {
        $imgPath = UPLOAD_DIR . $prod['imagen'];
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
    }

    $stmt = $db->prepare('DELETE FROM productos WHERE id = ?');
    $stmt->execute([$id]);

    jsonResponse(['success' => true]);
}

// =============================================
// Helper: Slug unico para productos
// =============================================
function uniqueSlugProducto(PDO $db, string $slug, ?int $excludeId = null): string {
    $original = $slug;
    $counter = 1;

    while (true) {
        $sql = 'SELECT id FROM productos WHERE slug = ?';
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
