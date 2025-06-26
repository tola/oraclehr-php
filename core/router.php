<?php
function route_to_page()
{
    $page = $_GET['page'] ?? 'dashboard';

    $public_pages = ['login'];
    $allowed_pages = ['login', 'dashboard', 'employees', 'departments', 'jobs'];

    if (!in_array($page, $public_pages) && !is_authenticated()) {
        header('Location: index.php?page=login');
        exit;
    }

    if (!in_array($page, $allowed_pages)) {
        http_response_code(404);
        echo "<div class='alert alert-danger mt-4'>404 Page Not Found</div>";
        return;
    }

    include "views/$page.php";
}
