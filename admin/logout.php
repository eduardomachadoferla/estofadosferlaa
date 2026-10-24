<?php
require_once __DIR__ . '/../config/admin_lib.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }
exigir_csrf();
$_SESSION=[];
if (ini_get('session.use_cookies')) {
    $params=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);
}
session_destroy();
header('Location: '.site_url('admin/login.php'));exit;
