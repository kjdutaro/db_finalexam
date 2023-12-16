<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include 'functions.php';

$incomingDocuments = getDocuments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document Tracking System</title>
</head>
<body>

<div class="container">
    <h2>Welcome to the Document Tracking System</h2>

    <div class="incoming-documents">
        <h3>Incoming Documents</h3>
        <ul>
            <?php foreach ($incomingDocuments as $document): ?>
                <li>
                    <strong>Document ID:</strong> <?= $document["document_id"] ?><br>
                    <strong>Status:</strong> <?= $document["status_id"] ?><br>
                    <strong>Content:</strong> <?= $document["content"] ?><br>
                    <strong>Date Created:</strong> <?= $document["date_created"] ?><br>
                    <strong>Origin Office:</strong> <?= $document["origin_office"] ?><br>
                    <strong>Target Recipient:</strong> <?= $document["target_recipient"] ?><br>
                    <a href='mark_accomplished.php?document_id=<?= $document["document_id"] ?>'>Mark Accomplished</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="create-outgoing">
        <h3>Create Outgoing Document</h3>
        <form method='post' action='create_outgoing.php' enctype='multipart/form-data'>
            <label for='origin_office'>Originating Office:</label>
            <input type='text' name='origin_office' required><br>

            <label for='target_recipient'>Target Recipient:</label>
            <input type='text' name='target_recipient' required><br>

            <label for='document_file'>Upload Document:</label>
            <input type='file' name='document_file' required><br>

            <button type='submit'>Create Document</button>
        </form>
    </div>

    <p><a href='logout.php'>Logout</a></p>
</div>

</body>
</html>
