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
    $file = $_FILES['document_file'];

    $uploadedFile = fileUpload($file);
    $filename = basename($file['name']);

    if ($uploadedFile == true) {
        $success = sendDocument($targetRecipient, $originOffice, $uploadedFile, $filename);

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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Document Tracking System</title>

    <script>
        //for panels
        function openPanel(panelId) {
            var panels = document.querySelectorAll('.panel');
            panels.forEach(function(panel) {
                panel.style.display = 'none';
            });
            var panel = document.getElementById(panelId);
            panel.style.display = 'block';
        }
        //for download
        function downloadDocument(filePath, fileName) {
            var link = document.createElement('a');
            link.href = 'doc_validator.php?file=' + encodeURIComponent(filePath);
            link.download = fileName;
            link.click();
        }
    </script>
</head>

<body>

    <div class="container mt-5">
        <h2>Hello, <?php echo $_SESSION['user_name'] ?>!</h2>
        <div style="height: 20px;"></div>
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="btn-group">
                    <button onclick="openPanel('create-outgoing')" class="btn btn-primary">Create Outgoing
                        Document</button>
                    <button onclick="openPanel('incoming-documents')" class="btn btn-info">Inbox</button>
                    <button onclick="openPanel('outbox')" class="btn btn-warning">Outbox</button>
                </div>
            </div>
            <div class="col-md-4">
                <form method="post" action="home.php">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
            <div style="height: 50px;"></div>
        </div>
    </div>

    <div class="container">

        <!-- Outgoing -->

        <section id="create-outgoing" class="panel" style="display: none;">
            <div class="container">
                <h3 class="mb-4">Create Outgoing Document</h3>
                <form method="post" action="home.php" enctype="multipart/form-data">

                    <div class="form-group">
                        <label for="origin_office">Originating Office:</label>
                        <input type="text" class="form-control" name="origin_office" required>
                    </div>

                    <div class="form-group">
                        <label for="target_recipient">Target Recipient:</label>
                        <input type="text" class="form-control" name="target_recipient" required>
                    </div>

                    <div class="form-group">
                        <label for="document_file">Upload Document:</label>
                        <input type="file" class="form-control-file" name="document_file" required>
                    </div>

                    <button type="submit" class="btn btn-success" name="create_outgoing">Send Document</button>

                </form>

            </div>
        </section>

        <!-- Inbox -->
        <section id="incoming-documents" class="panel">
            <h3>Incoming Documents</h3>
            <div style="height: 20px;"></div>
            <?php
            $inboxDocuments = getInboxDocuments();

            if (empty($inboxDocuments)) {
                echo "<p>No documents in your inbox.</p>";
            } else {
                echo "<form method='post' action='doc_handler.php'>";
                echo "<table class='table table-bordered'>";
                echo "<thead class='text-center'>";
                echo "<tr>";
                echo "<th>Date Created</th>";
                echo "<th>File</th>";
                echo "<th>Sender</th>";
                echo "<th>Originating Office</th>";
                echo "<th colspan='2'>Status</th>";
                echo "<th>Done</th>";
                echo "<th colspan='7'>Save Changes</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody class='text-center'>";
                foreach ($inboxDocuments as $document) {
                    $filename = $document['file_name'];
                    $filepath = $document['file_path'];
                    echo "<tr>";
                    echo "<td>{$document['DateCreated']}</td>";
                    echo "<td><a href='javascript:void(0);' onclick='downloadDocument(\"$filepath\", \"$filename\")'>$filename</a></td>";
                    echo "<td>{$document['name']}</td>";
                    echo "<td>{$document['origin_office']}</td>";
                    echo "<td colspan='2'><input type='text' class='form-control' name='comment[{$document['status']}]' style='width: 100%;'></td>";
                    echo "<td><input type='checkbox' name='accomplished[]' value='{$document['isAccomplished']}' id='chk{$document['document_id']}'></td>";
                    echo "<td colspan='7'><button type='submit' class='btn btn-primary' name='save_comments'>Save Changes</button></td>";
                    echo "<td style='display:none'>{$document['track_id']}</td>";

                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</form>";
            }
            ?>
        </section>

        <!-- Outbox -->
        <section id="outbox" class="panel" style="display: none;">
            <h3>Outbox</h3>
            <div style="height: 20px;"></div>
            <?php
            $outgoingDocuments = getOutboxDocuments();

            if (empty($outgoingDocuments)) {
                echo "<p>No documents in your outbox.</p>";
            } else {
                echo "<table class='table table-bordered'>";
                echo "<thead class='text-center'>";
                echo "<tr>";
                echo "<th>Date Created</th>";
                echo "<th>File</th>";
                echo "<th>Name</th>";
                echo "<th>Originating Office</th>";
                echo "<th>Status</th>";
                echo "<th>Accomplished</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody class='text-center'>";
                foreach ($outgoingDocuments as $document) {
                    $filename = $document['file_name'];
                    echo "<tr>";
                    echo "<td>{$document['DateCreated']}</td>";
                    echo "<td><a href='javascript:void(0);' onclick='downloadDocument(\"{$document['file_path']}\")'>$filename</a></td>";
                    echo "<td>{$document['name']}</td>";
                    echo "<td>{$document['origin_office']}</td>";
                    echo "<td>{$document['status']}</td>";
                    echo "<td>" . ($document['isAccomplished'] ? 'Yes' : 'No') . "</td>";
                    echo "<td style='display:none'>{$document['track_id']}</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";

            }
            ?>
        </section>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>