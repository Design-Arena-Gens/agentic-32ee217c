<?php
$unread = get_unread_count($conn, $_SESSION['user_id']);
?>
<nav class="navbar navbar-dark bg-info">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-building"></i> iSCSS - Office Staff Portal
        </a>
        <div class="d-flex align-items-center">
            <a href="inbox.php" class="btn btn-outline-light btn-sm me-2 position-relative">
                <i class="bi bi-envelope"></i> Inbox
                <?php if ($unread > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $unread; ?>
                    </span>
                <?php endif; ?>
            </a>
            <span class="text-white me-3">
                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>
