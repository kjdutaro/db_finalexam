<?php
include 'doc_handler.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

//outgoing
if (isset($_POST['create_outgoing'])) {

    $originOffice = $_POST['origin_office'];
    $targetRecipient = $_POST['target_recipient'];

    $uploadedFile = fileUpload($_FILES['document_file']);

    if ($uploadedFile == true) {
        $success = sendDocument($originOffice, $targetRecipient, $uploadedFile);

        echo '<script>';
        if ($success) {
            echo 'alert("Document sent successfully!");';
        } else {
            echo 'alert("Failed to send the document. Please try again. here problemo again ");' ;
        }
        echo '</script>';
    } else {
        echo '<script>alert("Failed to upload the document. Please try again. file not uploaded to uploads");</script>';
    }
}
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

        <!-- Outgoing -->

        <section class="create-outgoing">
            <h3>Create Outgoing Document</h3>
            <form method='post' action='home.php' enctype='multipart/form-data'>
                <label for='origin_office'>Originating Office:</label>
                <input type='text' name='origin_office' required><br>

                <label for='target_recipient'>Target Recipient:</label>
                <input type='text' name='target_recipient' required><br>

                <label for='document_file'>Upload Document:</label>
                <input type='file' name='document_file' required><br>

                <button type='submit' name='create_outgoing'>Create Document</button>
            </form>


        </section>

        <!-- Inbox -->
        <section class="incoming-documents">
            <h3>Incoming Documents</h3>

            <?php
            $incomingDocuments = getInboxDocuments();

            if (empty($incomingDocuments)) {
                echo "<p>No documents in your inbox.</p>";
            } else {
                echo "<ul>";

                foreach ($incomingDocuments as $document) {
                    echo "<li>";
                    echo "<strong>Document ID:</strong> {$document["document_id"]}<br>";
                    echo "<strong>Status:</strong> {$document["status_id"]}<br>";
                    echo "<strong>Content:</strong> {$document["content"]}<br>";
                    echo "<strong>Date Created:</strong> {$document["date_created"]}<br>";
                    echo "<strong>Origin Office:</strong> {$document["origin_office"]}<br>";
                    echo "<strong>Target Recipient:</strong> {$document["target_recipient"]}<br>";
                    echo "<a href='document_tracking.php?mark_accomplished={$document["document_id"]}'>Mark Accomplished</a>";
                    echo "</li>";
                }

                echo "</ul>";
            }
            ?>
        </section>

        <!-- outbox -->
        <section id="outbox">
            <h3>Outbox</h3>

            <?php
            if (empty($outgoingDocuments)) {
                echo "<p>No documents in your outbox.</p>";
            } else {
                foreach ($outgoingDocuments as $document) {
                    echo "<p>";
                    echo "Document ID: {$document['document_id']}<br>";
                    echo "Filename: {$document['filename']}<br>";
                    echo "Date Created: {$document['DateCreated']}<br>";
                    echo "Status: {$document['status']}<br>";
                    echo "Is Accomplished: " . ($document['isAccomplished'] ? 'Yes' : 'No') . "<br>";
                    echo "Origin Office: {$document['origin_office']}<br>";
                    echo "Comments: {$document['comments']}<br>";
                    echo "<a href='#'>View Document</a><br>";
                    echo "</p>";
                }
            }
            ?>
        </section>


        <p><a href='document_tracking.php?logout'>Logout</a></p>
    </div>

</body>

</html>