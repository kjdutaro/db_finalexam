<?php
if (isset($_GET['file'])) {
    $filePath = $_GET['file'];

    // Validate and sanitize the file path to prevent directory traversal attacks
    $filePath = realpath('your_document_directory/' . $filePath);

    if ($filePath !== false && strpos($filePath, 'your_document_directory/') === 0) {
        // Set appropriate headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));

        // Output the file content
        readfile($filePath);
        exit; // Exit to prevent further output
    } else {
        // Invalid or unsafe file path
        header("HTTP/1.0 404 Not Found");
        echo 'File not found or invalid request.';
    }
} else {
    // Missing 'file' parameter in the request
    header("HTTP/1.0 400 Bad Request");
    echo 'Invalid request.';
}
