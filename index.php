<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iSCSS - Student Collaboration and Support System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .welcome-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-card p-5">
            <div class="text-center mb-4">
                <h1 class="display-4 fw-bold text-primary">iSCSS</h1>
                <p class="lead text-muted">Student Collaboration and Support System</p>
            </div>

            <div class="row text-center mb-4">
                <div class="col-md-3">
                    <div class="feature-icon">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <h5 class="mt-2">Multi-Role Access</h5>
                    <p class="text-muted small">Admin, Students, Teachers & Staff</p>
                </div>
                <div class="col-md-3">
                    <div class="feature-icon">
                        <i class="bi bi-envelope-paper"></i>
                    </div>
                    <h5 class="mt-2">Inquiry System</h5>
                    <p class="text-muted small">Submit and Track Requests</p>
                </div>
                <div class="col-md-3">
                    <div class="feature-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h5 class="mt-2">Messaging</h5>
                    <p class="text-muted small">Real-time Communication</p>
                </div>
                <div class="col-md-3">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="mt-2">Secure</h5>
                    <p class="text-muted small">Role-based Permissions</p>
                </div>
            </div>

            <div class="text-center">
                <a href="login.php" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-box-arrow-in-right"></i> Login to System
                </a>
            </div>

            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="text-center mb-3">Demo Credentials</h6>
                <div class="row text-center">
                    <div class="col-12">
                        <strong>Admin:</strong> admin / password
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted small mb-0">
                    <strong>Note:</strong> This is a demonstration of a PHP-based student support system.
                    Due to Vercel's static hosting limitations, the full PHP functionality (database, sessions)
                    is not available in this deployment. For full functionality, deploy to a PHP-enabled server
                    with MySQL support.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
