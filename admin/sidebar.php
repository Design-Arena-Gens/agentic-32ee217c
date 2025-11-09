<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_students.php">
                    <i class="bi bi-people"></i> Manage Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_teachers.php">
                    <i class="bi bi-person-badge"></i> Manage Teachers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_staff.php">
                    <i class="bi bi-building"></i> Manage Office Staff
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view_all_inquiries.php">
                    <i class="bi bi-envelope"></i> All Inquiries
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view_messages.php">
                    <i class="bi bi-chat-dots"></i> All Messages
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    height: calc(100vh - 56px);
    overflow-y: auto;
}

.sidebar .nav-link {
    color: #333;
    padding: 10px 15px;
}

.sidebar .nav-link:hover {
    background-color: #e9ecef;
}

.sidebar .nav-link i {
    margin-right: 8px;
}
</style>
