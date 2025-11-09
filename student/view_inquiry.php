<?php
require_once '../config.php';
check_role(['student']);

$conn = db_connect();
$user = get_current_user($conn);

$inquiry_id = intval($_GET['id']);

// Get inquiry details (verify ownership)
$inquiry_query = "
    SELECT i.*, u.full_name as recipient_name, u.email as recipient_email
    FROM inquiries i
    JOIN users u ON i.recipient_id = u.id
    WHERE i.id = $inquiry_id AND i.student_id = {$user['id']}
";
$inquiry_result = mysqli_query($conn, $inquiry_query);

if (mysqli_num_rows($inquiry_result) == 0) {
    header("Location: my_inquiries.php");
    exit();
}

$inquiry = mysqli_fetch_assoc($inquiry_result);

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_message'])) {
    $reply_message = sanitize($conn, $_POST['reply_message']);

    $message_query = "INSERT INTO messages (inquiry_id, sender_id, recipient_id, message)
                      VALUES ($inquiry_id, {$user['id']}, {$inquiry['recipient_id']}, '$reply_message')";

    if (mysqli_query($conn, $message_query)) {
        header("Location: view_inquiry.php?id=$inquiry_id");
        exit();
    }
}

// Get messages/replies
$messages_query = "
    SELECT m.*, u1.full_name as sender_name, u1.role as sender_role
    FROM messages m
    JOIN users u1 ON m.sender_id = u1.id
    WHERE m.inquiry_id = $inquiry_id
    ORDER BY m.created_at ASC
";
$messages = mysqli_query($conn, $messages_query);

// Mark messages as read
$mark_read_query = "UPDATE messages SET is_read = 1 WHERE inquiry_id = $inquiry_id AND recipient_id = {$user['id']}";
mysqli_query($conn, $mark_read_query);

// Get tagged users
$tags_query = "
    SELECT u.full_name, u.email
    FROM inquiry_tags it
    JOIN users u ON it.tagged_user_id = u.id
    WHERE it.inquiry_id = $inquiry_id
";
$tags = mysqli_query($conn, $tags_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inquiry - iSCSS</title>
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
                    <h1 class="h2">Inquiry Details</h1>
                    <a href="my_inquiries.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><?php echo htmlspecialchars($inquiry['subject']); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>To:</strong> <?php echo htmlspecialchars($inquiry['recipient_name']); ?>
                                </div>
                                <div class="mb-3">
                                    <strong>Your Message:</strong>
                                    <p class="mt-2"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>Sent on <?php echo format_date($inquiry['created_at']); ?></small>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Conversation</h5>
                            </div>
                            <div class="card-body">
                                <?php if (mysqli_num_rows($messages) > 0): ?>
                                    <?php while ($message = mysqli_fetch_assoc($messages)): ?>
                                        <div class="message-item border-bottom pb-3 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <strong>
                                                    <?php echo htmlspecialchars($message['sender_name']); ?>
                                                    <span class="badge bg-secondary"><?php echo ucfirst($message['sender_role']); ?></span>
                                                </strong>
                                                <small class="text-muted"><?php echo format_date($message['created_at']); ?></small>
                                            </div>
                                            <p class="mt-2 mb-0"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="text-muted">No replies yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($inquiry['status'] != 'closed'): ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Send Reply</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <textarea class="form-control" name="reply_message" rows="4" required placeholder="Type your message..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Send Reply
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Status</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Type:</strong>
                                    <span class="badge bg-<?php echo $inquiry['type'] == 'inquiry' ? 'info' : 'warning'; ?>">
                                        <?php echo ucfirst($inquiry['type']); ?>
                                    </span>
                                </p>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-<?php
                                        echo $inquiry['status'] == 'pending' ? 'warning' :
                                            ($inquiry['status'] == 'resolved' ? 'success' : 'secondary');
                                    ?>">
                                        <?php echo ucfirst($inquiry['status']); ?>
                                    </span>
                                </p>
                                <p><strong>Priority:</strong>
                                    <span class="badge bg-<?php
                                        echo $inquiry['priority'] == 'high' ? 'danger' :
                                            ($inquiry['priority'] == 'medium' ? 'warning' : 'secondary');
                                    ?>">
                                        <?php echo ucfirst($inquiry['priority']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <?php if (mysqli_num_rows($tags) > 0): ?>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Tagged Users</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <?php while ($tag = mysqli_fetch_assoc($tags)): ?>
                                        <li class="mb-2">
                                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($tag['full_name']); ?>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
