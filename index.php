<?php

ini_set('session.cookie_httponly', 1);
//ini_set('session.cookie_secure', 1); // Only on HTTPS!
ini_set('session.use_strict_mode', 1);
session_name('HRAPPSESSID');
session_start();

require_once 'core/auth.php';
handle_login_request();
require_once 'core/router.php';

$page = $_GET['page'] ?? 'dashboard';

if ($page === 'login') {
    // Render login without layout
    route_to_page();
} else {
    // Full layout
    include 'includes/header.php';
    route_to_page();
    include 'includes/footer.php';
}
