<?php
require_once '../config.php';
check_role(['admin']);

$conn = db_connect();
$user = get_current_user($conn);
$message = '';
$error = '';

// Handle add/edit teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $full_name = sanitize($conn, $_POST['full_name']);
    $email = sanitize($conn, $_POST['email']);
    $username = sanitize($conn, $_POST['username']);
    $faculty = sanitize($conn, $_POST['faculty']);
    $department = sanitize($conn, $_POST['department']);

    if ($_POST['action'] == 'add') {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, password, full_name, email, faculty, department, role)
                  VALUES ('$username', '$password', '$full_name', '$email', '$faculty', '$department', 'teacher')";

        if (mysqli_query($conn, $query)) {
            $message = 'Teacher added successfully!';
        } else {
            $error = 'Error adding teacher: ' . mysqli_error($conn);
        }
    } elseif ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);

        $query = "UPDATE users SET username = '$username', full_name = '$full_name', email = '$email',
                  faculty = '$faculty', department = '$department' WHERE id = $id AND role = 'teacher'";

        if (mysqli_query($conn, $query)) {
            $message = 'Teacher updated successfully!';
        } else {
            $error = 'Error updating teacher: ' . mysqli_error($conn);
        }
    }
}

// Handle toggle active status
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $query = "UPDATE users SET is_active = NOT is_active WHERE id = $id AND role = 'teacher'";

    if (mysqli_query($conn, $query)) {
        $message = 'Teacher status updated!';
    }
}

// Get all teachers
$teachers_query = "SELECT * FROM users WHERE role = 'teacher' ORDER BY created_at DESC";
$teachers = mysqli_query($conn, $teachers_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - iSCSS</title>
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
                    <h1 class="h2">Manage Teachers</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                        <i class="bi bi-plus-circle"></i> Add Teacher
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
                                        <th>Faculty</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($teacher = mysqli_fetch_assoc($teachers)): ?>
                                    <tr>
                                        <td><?php echo $teacher['id']; ?></td>
                                        <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['faculty']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['department']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $teacher['is_active'] ? 'success' : 'danger'; ?>">
                                                <?php echo $teacher['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick='editTeacher(<?php echo json_encode($teacher); ?>)'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?toggle=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-<?php echo $teacher['is_active'] ? 'danger' : 'success'; ?>"
                                               onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-<?php echo $teacher['is_active'] ? 'x-circle' : 'check-circle'; ?>"></i>
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

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Teacher</h5>
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
                            <label class="form-label">Faculty</label>
                            <input type="text" class="form-control" name="faculty" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Teacher Modal -->
    <div class="modal fade" id="editTeacherModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Teacher</h5>
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
                            <label class="form-label">Faculty</label>
                            <input type="text" class="form-control" name="faculty" id="edit_faculty" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" id="edit_department" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editTeacher(teacher) {
            document.getElementById('edit_id').value = teacher.id;
            document.getElementById('edit_username').value = teacher.username;
            document.getElementById('edit_full_name').value = teacher.full_name;
            document.getElementById('edit_email').value = teacher.email;
            document.getElementById('edit_faculty').value = teacher.faculty || '';
            document.getElementById('edit_department').value = teacher.department || '';

            var modal = new bootstrap.Modal(document.getElementById('editTeacherModal'));
            modal.show();
        }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>
