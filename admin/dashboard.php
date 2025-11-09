<?php
require_once '../config.php';
check_role(['admin']);

$conn = db_connect();
$user = get_current_user($conn);

// Get statistics
$total_students_query = "SELECT COUNT(*) as count FROM users WHERE role = 'student' AND is_active = 1";
$total_teachers_query = "SELECT COUNT(*) as count FROM users WHERE role = 'teacher' AND is_active = 1";
$total_staff_query = "SELECT COUNT(*) as count FROM users WHERE role = 'office_staff' AND is_active = 1";
$total_inquiries_query = "SELECT COUNT(*) as count FROM inquiries";
$pending_inquiries_query = "SELECT COUNT(*) as count FROM inquiries WHERE status = 'pending'";

$total_students = mysqli_fetch_assoc(mysqli_query($conn, $total_students_query))['count'];
$total_teachers = mysqli_fetch_assoc(mysqli_query($conn, $total_teachers_query))['count'];
$total_staff = mysqli_fetch_assoc(mysqli_query($conn, $total_staff_query))['count'];
$total_inquiries = mysqli_fetch_assoc(mysqli_query($conn, $total_inquiries_query))['count'];
$pending_inquiries = mysqli_fetch_assoc(mysqli_query($conn, $pending_inquiries_query))['count'];

// Get recent inquiries
$recent_inquiries_query = "
    SELECT i.*, u1.full_name as student_name, u2.full_name as recipient_name
    FROM inquiries i
    JOIN users u1 ON i.student_id = u1.id
    JOIN users u2 ON i.recipient_id = u2.id
    ORDER BY i.created_at DESC
    LIMIT 10
";
$recent_inquiries = mysqli_query($conn, $recent_inquiries_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - iSCSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-people-fill"></i> Students</h5>
                                <h2><?php echo $total_students; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-person-badge"></i> Teachers</h5>
                                <h2><?php echo $total_teachers; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-building"></i> Office Staff</h5>
                                <h2><?php echo $total_staff; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-envelope"></i> Inquiries</h5>
                                <h2><?php echo $total_inquiries; ?></h2>
                                <small>Pending: <?php echo $pending_inquiries; ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Inquiries</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Recipient</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($inquiry = mysqli_fetch_assoc($recent_inquiries)): ?>
                                    <tr>
                                        <td><?php echo $inquiry['id']; ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['recipient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $inquiry['type'] == 'inquiry' ? 'info' : 'warning'; ?>">
                                                <?php echo ucfirst($inquiry['type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php
                                                echo $inquiry['status'] == 'pending' ? 'warning' :
                                                    ($inquiry['status'] == 'resolved' ? 'success' : 'secondary');
                                            ?>">
                                                <?php echo ucfirst($inquiry['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_date($inquiry['created_at']); ?></td>
                                        <td>
                                            <a href="view_inquiry.php?id=<?php echo $inquiry['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
