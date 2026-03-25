<?php
/**
 * PintaPeg API - Generador de sitemap.xml dinamico
 *
 * GET /api/sitemap.php → Genera y sirve sitemap.xml
 */

define('PINTAPEG', true);
require_once __DIR__ . '/config.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://pintapeg.com';
$db = getDB();

// Paginas estaticas
$paginas = [
    ['url' => '/',              'priority' => '1.0', 'changefreq' => 'weekly'],
    ['url' => '/nosotros.php', 'priority' => '0.6', 'changefreq' => 'monthly'],
    ['url' => '/tienda.php',   'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => '/contacto.php', 'priority' => '0.5', 'changefreq' => 'monthly'],
];

// Productos activos
$stmt = $db->query("SELECT slug, created_at FROM productos WHERE activo = 1 ORDER BY created_at DESC");
$productos = $stmt->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Paginas estaticas
foreach ($paginas as $pagina) {
    echo '  <url>' . "\n";
    echo '    <loc>' . $baseUrl . $pagina['url'] . '</loc>' . "\n";
    echo '    <changefreq>' . $pagina['changefreq'] . '</changefreq>' . "\n";
    echo '    <priority>' . $pagina['priority'] . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

// Productos
foreach ($productos as $prod) {
    echo '  <url>' . "\n";
    echo '    <loc>' . $baseUrl . '/producto.php?slug=' . htmlspecialchars($prod['slug']) . '</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d', strtotime($prod['created_at'])) . '</lastmod>' . "\n";
    echo '    <changefreq>weekly</changefreq>' . "\n";
    echo '    <priority>0.8</priority>' . "\n";
    echo '  </url>' . "\n";
}

echo '</urlset>' . "\n";
