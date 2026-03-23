<?php
/**
 * PintaPeg - Funciones de autenticacion reutilizables
 * Incluir en endpoints que requieran autenticacion
 */

if (!defined('PINTAPEG')) {
    http_response_code(403);
    exit('Acceso denegado');
}

// =============================================
// Iniciar sesion segura
// =============================================
function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure'   => true,
            'cookie_samesite' => 'Strict',
            'use_strict_mode' => true,
        ]);
    }
}

// =============================================
// Verificar autenticacion
// =============================================
function requireAuth(): void {
    initSession();

    if (empty($_SESSION['user_id'])) {
        jsonResponse(['error' => 'No autenticado'], 401);
    }
}

// =============================================
// Verificar token CSRF
// =============================================
function requireCSRF(): void {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        jsonResponse(['error' => 'Token CSRF invalido'], 403);
    }
}
