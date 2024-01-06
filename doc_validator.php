<?php
if (isset($_GET['file'])) {
    $filename = $_GET['file'];
    $filePath = __DIR__ . '/uploads/' . $filename;

    $filePath = realpath($filePath);

    if ($filePath !== false && strpos($filePath, __DIR__ . '/uploads/') === 0) {
        if (file_exists($filePath)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Length: ' . filesize($filePath));

            readfile($filePath);
            exit; 
        } else {
            header("HTTP/1.0 404 Not Found");
            echo 'File not found.';
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo 'File not found or invalid request.';
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    echo 'Invalid request.';
}

