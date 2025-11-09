<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = db_connect();

    $username = sanitize($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username' AND is_active = 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'student':
                    header("Location: student/dashboard.php");
                    break;
                case 'teacher':
                    header("Location: teacher/dashboard.php");
                    break;
                case 'office_staff':
                    header("Location: staff/dashboard.php");
                    break;
            }
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Invalid username or password';
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - iSCSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">iSCSS</h2>
                            <p class="text-muted">Student Collaboration & Support System</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username / Registration Number</label>
                                <input type="text" class="form-control" id="username" name="username" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
