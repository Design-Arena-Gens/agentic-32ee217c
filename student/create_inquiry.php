<?php
require_once '../config.php';
check_role(['student']);

$conn = db_connect();
$user = get_current_user($conn);
$message = '';
$error = '';

// Get teachers and staff for recipient selection
$teachers_query = "SELECT id, full_name, faculty, department FROM users WHERE role = 'teacher' AND is_active = 1 ORDER BY full_name";
$teachers = mysqli_query($conn, $teachers_query);

$staff_query = "SELECT id, full_name, position, department FROM users WHERE role = 'office_staff' AND is_active = 1 ORDER BY full_name";
$staff = mysqli_query($conn, $staff_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient_id = intval($_POST['recipient_id']);
    $subject = sanitize($conn, $_POST['subject']);
    $inquiry_message = sanitize($conn, $_POST['message']);
    $type = sanitize($conn, $_POST['type']);
    $priority = sanitize($conn, $_POST['priority']);

    // Insert inquiry
    $query = "INSERT INTO inquiries (student_id, recipient_id, subject, message, type, priority)
              VALUES ({$user['id']}, $recipient_id, '$subject', '$inquiry_message', '$type', '$priority')";

    if (mysqli_query($conn, $query)) {
        $inquiry_id = mysqli_insert_id($conn);

        // Handle tagged users
        if (!empty($_POST['tagged_users'])) {
            foreach ($_POST['tagged_users'] as $tagged_id) {
                $tagged_id = intval($tagged_id);
                $tag_query = "INSERT INTO inquiry_tags (inquiry_id, tagged_user_id) VALUES ($inquiry_id, $tagged_id)";
                mysqli_query($conn, $tag_query);
            }
        }

        $message = 'Inquiry/Claim submitted successfully!';
        header("Location: view_inquiry.php?id=$inquiry_id");
        exit();
    } else {
        $error = 'Error submitting inquiry: ' . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Inquiry - iSCSS</title>
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
                    <h1 class="h2">Create New Inquiry/Claim</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Type</label>
                                        <select class="form-select" name="type" required>
                                            <option value="inquiry">Inquiry</option>
                                            <option value="claim">Claim</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Priority</label>
                                        <select class="form-select" name="priority" required>
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Send To</label>
                                        <select class="form-select" name="recipient_id" required>
                                            <option value="">Select Recipient</option>
                                            <optgroup label="Teachers">
                                                <?php while ($teacher = mysqli_fetch_assoc($teachers)): ?>
                                                    <option value="<?php echo $teacher['id']; ?>">
                                                        <?php echo htmlspecialchars($teacher['full_name']); ?> -
                                                        <?php echo htmlspecialchars($teacher['department']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </optgroup>
                                            <optgroup label="Office Staff">
                                                <?php while ($staff_member = mysqli_fetch_assoc($staff)): ?>
                                                    <option value="<?php echo $staff_member['id']; ?>">
                                                        <?php echo htmlspecialchars($staff_member['full_name']); ?> -
                                                        <?php echo htmlspecialchars($staff_member['position']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tag Additional Users (Optional)</label>
                                        <select class="form-select" name="tagged_users[]" multiple size="5">
                                            <?php
                                            mysqli_data_seek($teachers, 0);
                                            while ($teacher = mysqli_fetch_assoc($teachers)):
                                            ?>
                                                <option value="<?php echo $teacher['id']; ?>">
                                                    [Teacher] <?php echo htmlspecialchars($teacher['full_name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                            <?php
                                            mysqli_data_seek($staff, 0);
                                            while ($staff_member = mysqli_fetch_assoc($staff)):
                                            ?>
                                                <option value="<?php echo $staff_member['id']; ?>">
                                                    [Staff] <?php echo htmlspecialchars($staff_member['full_name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple users</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" class="form-control" name="subject" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea class="form-control" name="message" rows="8" required></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-send"></i> Submit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Guidelines</h6>
                                <ul class="small">
                                    <li>Choose the appropriate type: Inquiry for questions, Claim for formal requests</li>
                                    <li>Set priority based on urgency</li>
                                    <li>Select the main recipient who should handle your request</li>
                                    <li>Tag additional users who should be notified</li>
                                    <li>Be clear and concise in your message</li>
                                    <li>You will receive replies in your inbox</li>
                                </ul>
                            </div>
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
