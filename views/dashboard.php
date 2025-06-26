<?php
require_once 'core/auth.php';
require_once 'config/db.php';

// Restrict access to admin only
if (get_user_role() !== 'admin') {
    echo "<div class='alert alert-warning mt-4'>Access denied. Admins only.</div>";
    return;
}

// Helper to get record count from a table
function get_total($conn, $table)
{
    $query = "SELECT COUNT(*) AS total FROM $table";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    return $row['TOTAL'] ?? 0;
}

// Gather totals
$totals = [
    'Employees'   => get_total($conn, 'employees'),
    'Departments' => get_total($conn, 'departments'),
    'Jobs'        => get_total($conn, 'jobs'),
];


// ⬇️ Fetch recent hires (limit 5)
function get_recent_hires($conn, int $limit = 5): array
{
    $query = "SELECT employee_id, first_name || ' ' || last_name AS name, email, hire_date
              FROM employees
              ORDER BY hire_date DESC FETCH FIRST :limit ROWS ONLY";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':limit', $limit);
    oci_execute($stmt);
    $results = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $results[] = $row;
    }
    oci_free_statement($stmt);
    return $results;
}

// ⬇️ Fetch employee count grouped by department
function get_employee_totals_by_department($conn): array
{
    $query = "SELECT d.department_name, COUNT(e.employee_id) AS total
              FROM departments d
              LEFT JOIN employees e ON d.department_id = e.department_id
              GROUP BY d.department_name
              ORDER BY total DESC";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);
    $results = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $results[] = $row;
    }
    oci_free_statement($stmt);
    return $results;
}

$recentHires = get_recent_hires($conn);
$byDept = get_employee_totals_by_department($conn);

?>

<h2 class="mb-4">Admin Dashboard <button class="btn btn-outline-primary float-end" data-bs-toggle="modal" data-bs-target="#searchModal">
        🔍 Search Employee
    </button>
</h2>

<div class="row">
    <?php foreach ($totals as $label => $count): ?>
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-4 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= htmlspecialchars($label) ?></h5>
                    <p class="card-text display-5"><?= htmlspecialchars($count) ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="mt-4">
    <p class="text-muted">Data pulled live from Oracle HR schema.</p>
</div>



<h4 class="mt-5">📋 Recent Hires</h4>
<table class="table table-striped table-hover">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Hire Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentHires as $emp): ?>
            <tr>
                <td><?= $emp['EMPLOYEE_ID'] ?></td>
                <td><?= htmlspecialchars($emp['NAME']) ?></td>
                <td><?= htmlspecialchars($emp['EMAIL']) ?></td>
                <td><?= date('M d, Y', strtotime($emp['HIRE_DATE'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h4 class="mt-5">🏢 Employees by Department</h4>
<table class="table table-bordered table-sm shadow-sm">
    <thead class="table-secondary">
        <tr>
            <th>Department</th>
            <th>Total Employees</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($byDept as $dept): ?>
            <tr>
                <td><?= htmlspecialchars($dept['DEPARTMENT_NAME']) ?></td>
                <td><?= $dept['TOTAL'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title">Search Employee by Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="searchForm" class="row g-3 mb-3">
                    <div class="col-md-10">
                        <input type="text" id="searchName" class="form-control" placeholder="Enter full or partial name" required>
                        <input type="hidden" name="nonce" value="<?= generate_nonce('search_employee'); ?>">
                        <input type="hidden" id="csrf_token" value="<?= generate_csrf_token(); ?>">

                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
                <div id="searchResults"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const term = document.getElementById('searchName').value.trim();
        const token = document.getElementById('csrf_token').value;
        const resultsDiv = document.getElementById('searchResults');
        if (!term) return;

        resultsDiv.innerHTML = "<p>Loading...</p>";

        fetch(`ajax/search_employee.php?term=${encodeURIComponent(term)}`, {
                headers: {
                    'X-CSRF-Token': token
                }
            })
            .then(res => res.text())
            .then(html => resultsDiv.innerHTML = html)
            .catch(() => resultsDiv.innerHTML = "<p class='text-danger'>Error fetching results.</p>");
    });
</script>