<?php
/**
 * PintaPeg API - Subida y optimizacion de imagenes
 *
 * POST /api/upload.php → Subir imagen (admin)
 *
 * - Redimensiona a max 800px ancho
 * - Comprime al 80%
 * - Convierte a WebP (conserva alpha en PNG)
 * - Retorna nombre del archivo
 */

define('PINTAPEG', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth-helper.php';

requireAuth();
requireCSRF();
requireMethod('POST');

// Verificar que se envio un archivo
if (empty($_FILES['imagen'])) {
    jsonResponse(['error' => 'No se envio ninguna imagen'], 400);
}

$file = $_FILES['imagen'];

// Validar errores de subida
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errores = [
        UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamano maximo del servidor',
        UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamano maximo del formulario',
        UPLOAD_ERR_PARTIAL    => 'El archivo se subio parcialmente',
        UPLOAD_ERR_NO_FILE    => 'No se envio ningun archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta directorio temporal',
        UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
    ];
    $msg = $errores[$file['error']] ?? 'Error desconocido al subir';
    jsonResponse(['error' => $msg], 400);
}

// Validar tipo MIME
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, $tiposPermitidos)) {
    jsonResponse(['error' => 'Tipo de archivo no permitido. Solo JPG, PNG, WebP y GIF'], 400);
}

// Validar tamano (max 10MB)
if ($file['size'] > 10 * 1024 * 1024) {
    jsonResponse(['error' => 'El archivo no debe superar 10MB'], 400);
}

// Crear directorio si no existe
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Procesar imagen
$resultado = procesarImagen($file['tmp_name'], $mimeType);

if (isset($resultado['error'])) {
    jsonResponse($resultado, 500);
}

jsonResponse([
    'success' => true,
    'archivo' => $resultado['archivo'],
    'ruta' => '/uploads/productos/' . $resultado['archivo'],
]);

// =============================================
// Procesar imagen: redimensionar + WebP
// =============================================
function procesarImagen(string $tmpPath, string $mimeType): array {
    // Crear recurso de imagen segun tipo
    switch ($mimeType) {
        case 'image/jpeg':
            $imagen = imagecreatefromjpeg($tmpPath);
            break;
        case 'image/png':
            $imagen = imagecreatefrompng($tmpPath);
            break;
        case 'image/webp':
            $imagen = imagecreatefromwebp($tmpPath);
            break;
        case 'image/gif':
            $imagen = imagecreatefromgif($tmpPath);
            break;
        default:
            return ['error' => 'Tipo de imagen no soportado'];
    }

    if (!$imagen) {
        return ['error' => 'No se pudo procesar la imagen'];
    }

    // Obtener dimensiones originales
    $anchoOriginal = imagesx($imagen);
    $altoOriginal = imagesy($imagen);

    // Redimensionar si excede el ancho maximo
    if ($anchoOriginal > UPLOAD_MAX_WIDTH) {
        $ratio = UPLOAD_MAX_WIDTH / $anchoOriginal;
        $nuevoAncho = UPLOAD_MAX_WIDTH;
        $nuevoAlto = (int)round($altoOriginal * $ratio);

        $imagenRedimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        // Preservar transparencia para PNG
        if ($mimeType === 'image/png') {
            imagealphablending($imagenRedimensionada, false);
            imagesavealpha($imagenRedimensionada, true);
            $transparente = imagecolorallocatealpha($imagenRedimensionada, 0, 0, 0, 127);
            imagefill($imagenRedimensionada, 0, 0, $transparente);
        }

        imagecopyresampled(
            $imagenRedimensionada, $imagen,
            0, 0, 0, 0,
            $nuevoAncho, $nuevoAlto,
            $anchoOriginal, $altoOriginal
        );

        imagedestroy($imagen);
        $imagen = $imagenRedimensionada;
    } else {
        // Aun sin redimensionar, preservar alpha para PNG
        if ($mimeType === 'image/png') {
            imagealphablending($imagen, false);
            imagesavealpha($imagen, true);
        }
    }

    // Generar nombre unico
    $nombreArchivo = uniqid('prod_', true) . '.webp';
    $rutaDestino = UPLOAD_DIR . $nombreArchivo;

    // Guardar como WebP
    // Para PNG con transparencia, imagewebp conserva el canal alpha
    imagesavealpha($imagen, true);
    $ok = imagewebp($imagen, $rutaDestino, UPLOAD_QUALITY);
    imagedestroy($imagen);

    if (!$ok) {
        return ['error' => 'Error al guardar la imagen'];
    }

    return ['archivo' => $nombreArchivo];
}
