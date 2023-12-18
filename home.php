<?php
include 'doc_handler.php';

//auth
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout();
}

//outgoing
if (isset($_POST['create_outgoing'])) {

    $originOffice = $_POST['origin_office'];
    $targetRecipient = $_POST['target_recipient'];

    $uploadedFile = fileUpload($_FILES['document_file']);

    if ($uploadedFile == true) {
        $success = sendDocument($targetRecipient, $originOffice, $uploadedFile);

        echo '<script>';
        if ($success) {
            echo 'alert("Document sent successfully!");';
        } else {
            echo 'alert("Failed to send the document. Please try again.");';
        }
        echo '</script>';
    } else {
        echo '<script>alert("Failed to upload the document. Please try again.");</script>';
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
        <h2>Welcome, <?php echo $_SESSION['user_name'] ?> </h2>

        <button onclick="openPanel('create-outgoing')">Create Outgoing Document</button>
        <button onclick="openPanel('incoming-documents')">Inbox</button>
        <button onclick="openPanel('outbox')">Outbox</button>
        <section class="lbtn">
            <form method="post" action="home.php">
                <input type="hidden" name="logout" value="1">
                <button type="submit">Logout</button>
            </form>
        </section>
    </div>

    <div>

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
            $inboxDocuments = getInboxDocuments();

            if (empty($inboxDocuments)) {
                echo "<p>No documents in your inbox.</p>";
            } else {
                echo "<form method='post' action='doc_handler.php'>";
                echo "<table border='1'>";
                foreach ($inboxDocuments as $document) {
                    echo "<tr>";
                    echo "<td>{$document['DateCreated']}</td>";
                    echo "<td><a href='javascript:void(0);' onclick='openDocument(\"{$document['file_path']}\")'>View/Download</a></td>";
                    echo "<td>{$document['name']}</td>";
                    echo "<td>{$document['origin_office']}</td>";
                    echo "<td colspan='2'><input type='text' name='comment[{$document['status']}]' style='width: 100%;'></td>";

                    echo "<td><input type='checkbox' name='accomplished[]' value='{$document['isAccomplished']}' id='chk{$document['document_id']}'>";
                    echo "<label for='chk{$document['document_id']}'> Done</label></td>";


                    echo "<td colspan='7'><button type='submit' name='save_comments'>Save Changes</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
                echo "</form>";

                // js open docu in browser
                echo "<script>
        function openDocument(filePath) {
            window.open('doc_viewer.php?file=' + encodeURIComponent(filePath), '_blank');
        }
    </script>";
            }
            ?>
        </section>


        <!-- outbox -->
        <section id="outbox">
            <h3>Outbox</h3>

            <?php

            $outgoingDocuments = getOutboxDocuments();

            if (empty($outgoingDocuments)) {
                echo "<p>No documents in your outbox.</p>";
            } else {
                echo "<table border='1'>";
                foreach ($outgoingDocuments as $document) {
                    echo "<tr>";
                    echo "<td>{$document['DateCreated']}</td>";
                    echo "<td><a href='javascript:void(0);' onclick='openDocument(\"{$document['file_path']}\")'>View/Download</a></td>";
                    echo "<td>{$document['name']}</td>";
                    echo "<td>{$document['origin_office']}</td>";
                    echo "<td>{$document['status']}</td>";
                    echo "<td>" . ($document['isAccomplished'] ? 'Done' : 'Not Done') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                // js open docu in browser
                echo "<script>
        function openDocument(filePath) {
            window.open('doc_viewer.php?file=' + encodeURIComponent(filePath), '_blank');
        }
    </script>";
            }
            ?>
        </section>


    </div>

</body>

</html>