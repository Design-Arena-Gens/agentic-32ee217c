<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-building"></i> iSCSS - Admin Panel
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>
