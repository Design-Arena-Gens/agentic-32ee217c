<?php
require_once '../config.php';
check_role(['teacher']);

$conn = db_connect();
$user = get_current_user($conn);

$inquiry_id = intval($_GET['id']);

// Get inquiry details (verify recipient)
$inquiry_query = "
    SELECT i.*, u.full_name as student_name, u.email as student_email
    FROM inquiries i
    JOIN users u ON i.student_id = u.id
    WHERE i.id = $inquiry_id AND i.recipient_id = {$user['id']}
";
$inquiry_result = mysqli_query($conn, $inquiry_query);

if (mysqli_num_rows($inquiry_result) == 0) {
    header("Location: inquiries.php");
    exit();
}

$inquiry = mysqli_fetch_assoc($inquiry_result);

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_message'])) {
    $reply_message = sanitize($conn, $_POST['reply_message']);

    $message_query = "INSERT INTO messages (inquiry_id, sender_id, recipient_id, message)
                      VALUES ($inquiry_id, {$user['id']}, {$inquiry['student_id']}, '$reply_message')";

    if (mysqli_query($conn, $message_query)) {
        // Update inquiry status to in_progress if pending
        if ($inquiry['status'] == 'pending') {
            $update_status = "UPDATE inquiries SET status = 'in_progress' WHERE id = $inquiry_id";
            mysqli_query($conn, $update_status);
        }

        header("Location: view_inquiry.php?id=$inquiry_id");
        exit();
    }
}

// Handle status update
if (isset($_POST['update_status'])) {
    $new_status = sanitize($conn, $_POST['status']);
    $update_query = "UPDATE inquiries SET status = '$new_status' WHERE id = $inquiry_id";

    if (mysqli_query($conn, $update_query)) {
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
                    <a href="inquiries.php" class="btn btn-secondary">
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
                                    <strong>From:</strong> <?php echo htmlspecialchars($inquiry['student_name']); ?>
                                    (<?php echo htmlspecialchars($inquiry['student_email']); ?>)
                                </div>
                                <div class="mb-3">
                                    <strong>Message:</strong>
                                    <p class="mt-2"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>Received on <?php echo format_date($inquiry['created_at']); ?></small>
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
                                        <textarea class="form-control" name="reply_message" rows="4" required placeholder="Type your response..."></textarea>
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
                                <h6 class="mb-0">Update Status</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="update_status" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">Current Status</label>
                                        <select class="form-select" name="status">
                                            <option value="pending" <?php echo $inquiry['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="in_progress" <?php echo $inquiry['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="resolved" <?php echo $inquiry['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                            <option value="closed" <?php echo $inquiry['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm w-100">Update Status</button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Details</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Type:</strong>
                                    <span class="badge bg-<?php echo $inquiry['type'] == 'inquiry' ? 'info' : 'warning'; ?>">
                                        <?php echo ucfirst($inquiry['type']); ?>
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
