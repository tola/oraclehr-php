<?php include 'includes/header.php'; ?>
<div id="particles-js"></div>
<div class="d-flex justify-content-center align-items-center min-vh-100 position-relative">
    <form method="post" action="index.php?page=login" class="bg-white p-4 rounded shadow" style="min-width: 300px; z-index: 1;">
        <h3 class="mb-3 text-center">HR Portal Login</h3>
        <div class="mb-3">
            <input type="text" class="form-control" name="username" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
<!-- Particle JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script src="assets/js/particles-config.js"></script>
<?php include 'includes/footer.php'; ?>