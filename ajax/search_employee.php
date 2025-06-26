<?php
require_once '../core/auth.php';
require_once '../config/db.php';

$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!validate_csrf_token($token)) {
    http_response_code(403);
    echo "<p class='text-danger'>Invalid request (CSRF check failed).</p>";
    exit;
}

if (!validate_nonce('search_employee', $_POST['nonce'] ?? '')) {
    http_response_code(403);
    exit('Invalid CSRF nonce.');
}


// Auth check
if (!is_authenticated()) {
    http_response_code(403);
    echo "<p class='text-danger'>Unauthorized</p>";
    exit;
}

$term = trim($_GET['term'] ?? '');
if (empty($term)) {
    echo "<p class='text-warning'>Please enter a name to search.</p>";
    exit;
}

// Uppercase normalization for case-insensitive match
$term = strtoupper($term);

// Prepared statement with bind to avoid SQL injection
$query = "SELECT employee_id, first_name || ' ' || last_name AS name, email, job_id
          FROM employees
          WHERE UPPER(first_name || ' ' || last_name) LIKE '%' || :term || '%'";

$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':term', $term);
oci_execute($stmt);

$hasResults = false;
echo "<table class='table table-sm table-bordered'>
        <thead class='table-light'>
          <tr><th>ID</th><th>Name</th><th>Email</th><th>Job</th></tr>
        </thead>
        <tbody>";

while ($row = oci_fetch_assoc($stmt)) {
    $hasResults = true;
    echo "<tr>
            <td>" . (int)$row['EMPLOYEE_ID'] . "</td>
            <td>" . htmlspecialchars($row['NAME']) . "</td>
            <td>" . htmlspecialchars($row['EMAIL']) . "</td>
            <td>" . htmlspecialchars($row['JOB_ID']) . "</td>
          </tr>";
}
oci_free_statement($stmt);

if (!$hasResults) {
    echo "<tr><td colspan='4' class='text-center text-muted'>No results found.</td></tr>";
}

echo "</tbody></table>";
