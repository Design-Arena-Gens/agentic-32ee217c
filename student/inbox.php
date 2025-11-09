<?php
require_once '../config.php';
check_role(['student']);

$conn = db_connect();
$user = get_current_user($conn);

// Get messages for student
$messages_query = "
    SELECT m.*, u.full_name as sender_name, i.subject as inquiry_subject, i.id as inquiry_id
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    JOIN inquiries i ON m.inquiry_id = i.id
    WHERE m.recipient_id = {$user['id']}
    ORDER BY m.created_at DESC
";
$messages = mysqli_query($conn, $messages_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - iSCSS</title>
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
                    <h1 class="h2">Inbox</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="list-group">
                            <?php if (mysqli_num_rows($messages) > 0): ?>
                                <?php while ($message = mysqli_fetch_assoc($messages)): ?>
                                    <a href="view_inquiry.php?id=<?php echo $message['inquiry_id']; ?>"
                                       class="list-group-item list-group-item-action <?php echo !$message['is_read'] ? 'list-group-item-primary' : ''; ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                <?php if (!$message['is_read']): ?>
                                                    <i class="bi bi-envelope-fill text-primary"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-envelope-open"></i>
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($message['inquiry_subject']); ?>
                                            </h6>
                                            <small><?php echo format_date($message['created_at']); ?></small>
                                        </div>
                                        <p class="mb-1">
                                            <strong>From:</strong> <?php echo htmlspecialchars($message['sender_name']); ?>
                                        </p>
                                        <small><?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>...</small>
                                    </a>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                                    <p class="text-muted mt-3">No messages yet</p>
                                </div>
                            <?php endif; ?>
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
