<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="create_inquiry.php">
                    <i class="bi bi-plus-circle"></i> New Inquiry/Claim
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_inquiries.php">
                    <i class="bi bi-list-ul"></i> My Inquiries
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inbox.php">
                    <i class="bi bi-envelope"></i> Inbox
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
