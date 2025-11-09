<?php
// Simple router for Vercel PHP deployment
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove leading slash
$request_uri = ltrim($request_uri, '/');

// Default to login page
if (empty($request_uri) || $request_uri === 'index.php') {
    header("Location: /login.php");
    exit();
}

// Map the request to the actual file
$file_path = __DIR__ . '/../' . $request_uri;

// Check if file exists
if (file_exists($file_path) && is_file($file_path)) {
    // Include the requested file
    require $file_path;
} else {
    // 404 Not Found
    http_response_code(404);
    echo "404 - Page Not Found";
}
?>
