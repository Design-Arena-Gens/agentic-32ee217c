<?php
require_once '../config.php';
check_role(['teacher']);

$conn = db_connect();
$user = get_current_user($conn);

// Get inquiries addressed to this teacher
$inquiries_query = "
    SELECT i.*, u.full_name as student_name
    FROM inquiries i
    JOIN users u ON i.student_id = u.id
    WHERE i.recipient_id = {$user['id']}
    ORDER BY i.created_at DESC
";
$inquiries = mysqli_query($conn, $inquiries_query);

// Get statistics
$total_inquiries = mysqli_num_rows($inquiries);
$pending_count_query = "SELECT COUNT(*) as count FROM inquiries WHERE recipient_id = {$user['id']} AND status = 'pending'";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_count_query))['count'];

$unread_count = get_unread_count($conn, $user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - iSCSS</title>
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
                    <h1 class="h2">Teacher Dashboard</h1>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-envelope"></i> Total Inquiries</h5>
                                <h2><?php echo $total_inquiries; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-clock"></i> Pending</h5>
                                <h2><?php echo $pending_count; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-chat-dots"></i> Unread Messages</h5>
                                <h2><?php echo $unread_count; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Student Inquiries</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    mysqli_data_seek($inquiries, 0);
                                    while ($inquiry = mysqli_fetch_assoc($inquiries)):
                                    ?>
                                    <tr>
                                        <td><?php echo $inquiry['id']; ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['student_name']); ?></td>
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
                                        <td>
                                            <span class="badge bg-<?php
                                                echo $inquiry['priority'] == 'high' ? 'danger' :
                                                    ($inquiry['priority'] == 'medium' ? 'warning' : 'secondary');
                                            ?>">
                                                <?php echo ucfirst($inquiry['priority']); ?>
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
