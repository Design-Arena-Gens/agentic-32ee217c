<?php
require_once '../config.php';
check_role(['admin']);

$conn = db_connect();
$user = get_current_user($conn);
$message = '';
$error = '';

// Handle add/edit staff
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $full_name = sanitize($conn, $_POST['full_name']);
    $email = sanitize($conn, $_POST['email']);
    $username = sanitize($conn, $_POST['username']);
    $position = sanitize($conn, $_POST['position']);
    $department = sanitize($conn, $_POST['department']);

    if ($_POST['action'] == 'add') {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, password, full_name, email, position, department, role)
                  VALUES ('$username', '$password', '$full_name', '$email', '$position', '$department', 'office_staff')";

        if (mysqli_query($conn, $query)) {
            $message = 'Office staff added successfully!';
        } else {
            $error = 'Error adding staff: ' . mysqli_error($conn);
        }
    } elseif ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);

        $query = "UPDATE users SET username = '$username', full_name = '$full_name', email = '$email',
                  position = '$position', department = '$department' WHERE id = $id AND role = 'office_staff'";

        if (mysqli_query($conn, $query)) {
            $message = 'Office staff updated successfully!';
        } else {
            $error = 'Error updating staff: ' . mysqli_error($conn);
        }
    }
}

// Handle toggle active status
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $query = "UPDATE users SET is_active = NOT is_active WHERE id = $id AND role = 'office_staff'";

    if (mysqli_query($conn, $query)) {
        $message = 'Staff status updated!';
    }
}

// Get all office staff
$staff_query = "SELECT * FROM users WHERE role = 'office_staff' ORDER BY created_at DESC";
$staff = mysqli_query($conn, $staff_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Office Staff - iSCSS</title>
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
                    <h1 class="h2">Manage Office Staff</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        <i class="bi bi-plus-circle"></i> Add Staff
                    </button>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($staff_member = mysqli_fetch_assoc($staff)): ?>
                                    <tr>
                                        <td><?php echo $staff_member['id']; ?></td>
                                        <td><?php echo htmlspecialchars($staff_member['username']); ?></td>
                                        <td><?php echo htmlspecialchars($staff_member['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($staff_member['email']); ?></td>
                                        <td><?php echo htmlspecialchars($staff_member['position']); ?></td>
                                        <td><?php echo htmlspecialchars($staff_member['department']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $staff_member['is_active'] ? 'success' : 'danger'; ?>">
                                                <?php echo $staff_member['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick='editStaff(<?php echo json_encode($staff_member); ?>)'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?toggle=<?php echo $staff_member['id']; ?>" class="btn btn-sm btn-<?php echo $staff_member['is_active'] ? 'danger' : 'success'; ?>"
                                               onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-<?php echo $staff_member['is_active'] ? 'x-circle' : 'check-circle'; ?>"></i>
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

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Office Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position (e.g., Dean, Head of Department)</label>
                            <input type="text" class="form-control" name="position" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Office Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="edit_username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" name="position" id="edit_position" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" id="edit_department" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editStaff(staff) {
            document.getElementById('edit_id').value = staff.id;
            document.getElementById('edit_username').value = staff.username;
            document.getElementById('edit_full_name').value = staff.full_name;
            document.getElementById('edit_email').value = staff.email;
            document.getElementById('edit_position').value = staff.position || '';
            document.getElementById('edit_department').value = staff.department || '';

            var modal = new bootstrap.Modal(document.getElementById('editStaffModal'));
            modal.show();
        }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>
