<?php
require_once '../config.php';
check_role(['admin']);

$conn = db_connect();
$user = get_current_user($conn);

// Get all messages
$messages_query = "
    SELECT m.*, u1.full_name as sender_name, u2.full_name as recipient_name,
           i.subject as inquiry_subject
    FROM messages m
    JOIN users u1 ON m.sender_id = u1.id
    JOIN users u2 ON m.recipient_id = u2.id
    JOIN inquiries i ON m.inquiry_id = i.id
    ORDER BY m.created_at DESC
";
$messages = mysqli_query($conn, $messages_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Messages - iSCSS</title>
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
                    <h1 class="h2">All Messages</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sender</th>
                                        <th>Recipient</th>
                                        <th>Inquiry Subject</th>
                                        <th>Message Preview</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($message = mysqli_fetch_assoc($messages)): ?>
                                    <tr>
                                        <td><?php echo $message['id']; ?></td>
                                        <td><?php echo htmlspecialchars($message['sender_name']); ?></td>
                                        <td><?php echo htmlspecialchars($message['recipient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($message['inquiry_subject']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($message['message'], 0, 50)); ?>...</td>
                                        <td>
                                            <span class="badge bg-<?php echo $message['is_read'] ? 'success' : 'warning'; ?>">
                                                <?php echo $message['is_read'] ? 'Read' : 'Unread'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_date($message['created_at']); ?></td>
                                        <td>
                                            <a href="view_inquiry.php?id=<?php echo $message['inquiry_id']; ?>" class="btn btn-sm btn-primary">
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
