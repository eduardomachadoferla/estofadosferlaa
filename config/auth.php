<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'httponly' => true, 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax', 'path' => '/',
    ]);
    session_start();
}
function admin_logado(): bool {
    return isset($_SESSION['admin_id']) && is_int($_SESSION['admin_id']);
}
function exigir_login(): void {
    if (!admin_logado()) {
        header('Location: ' . site_url('admin/login.php'));
        exit;
    }
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function exigir_csrf(): void {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403); exit('Sessão expirada ou formulário inválido. Atualize a página e tente novamente.');
    }
}
function flash(string $tipo, string $mensagem): void {
    $_SESSION['flash'] = ['tipo'=>$tipo, 'mensagem'=>$mensagem];
}
function ler_flash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
function admin_redirect(string $path = 'admin/index.php'): never {
    header('Location: ' . site_url($path)); exit;
}
