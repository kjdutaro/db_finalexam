<?php
include 'doc_logic.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Document Tracking System</title>

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
            $end = "recipient";

            if (empty($inboxDocuments)) {
                echo "<p>No documents in your inbox.</p>";
            } else {
                echo "<table class='table table-bordered'>";
                echo "<thead class='text-center'>";
                echo "<tr>";
                echo "<th>Date Created</th>";
                echo "<th>File</th>";
                echo "<th>Sender</th>";
                echo "<th>Originating Office</th>";
                echo "<th colspan='2'>Status</th>";
                echo "<th>Done</th>";
                echo "<th colspan='7'>Actions</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody class='text-center'>";
                foreach ($inboxDocuments as $document) {
                    $filename = $document['file_name'];
                    echo "<tr>";
                    echo "<td>{$document['DateCreated']}</td>";
                    echo "<td><a href='home.php?doc_id={$document['document_id']}'>$filename</a></td>";
                    echo "<td>{$document['name']}</td>";
                    echo "<td>{$document['origin_office']}</td>";

                    echo "<form method='post' action='home.php'>";
                    echo "<td colspan='2'><input type='text' class='form-control' name='comment' style='width: 100%;' value='" . $document['status'] . "'></td>";
                    echo "<td><input type='checkbox' name='done' value='1' " . ($document['isAccomplished'] == 1 ? 'checked' : '') . "></td>";
                    echo "<td style='display:none'><input name='trackid' value='{$document['track_id']}'></td>";
                    echo "<td colspan='7'><button type='submit' name='save_changes' class='btn btn-sm btn-primary'>Save Changes</button></form>
                    <form method='post' action='home.php' onsubmit='return confirmDelete()'>
                    <input style='display:none' name='trackid' value='{$document['track_id']}'>
                    <button type='submit' name='delete_inbox' class='btn btn-sm btn-danger'>Delete</button></td>";
                    echo "</form>";


                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
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
                echo "<th>Sent To</th>";
                echo "<th>Originating Office</th>";
                echo "<th>Status</th>";
                echo "<th>Accomplished</th>";
                echo "<th>Actions</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody class='text-center'>";
                foreach ($outgoingDocuments as $document) {
                    $filename = $document['file_name'];
                    echo "<tr>";
                    echo "<td>{$document['DateCreated']}</td>";
                    echo "<td><a href='home.php?doc_id={$document['document_id']}' name>$filename</a></td>";
                    echo "<td>{$document['name']}</td>";
                    echo "<td>{$document['origin_office']}</td>";
                    echo "<td>{$document['status']}</td>";
                    echo "<td>" . ($document['isAccomplished'] ? 'Yes' : 'No') . "</td>";

                    echo "<form method='post' action='home.php' onsubmit='return confirmDelete()'>";
                    echo "<td style='display:none'><input name='trackid' value='{$document['track_id']}'></td>";
                    echo "<td colspan='7'><button type='submit' name='delete_outbox' class='btn btn-sm btn-danger'>Delete</button></td>";
                    echo "</form>";

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