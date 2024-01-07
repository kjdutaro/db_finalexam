<?php
include('doc_functions.php');

//auth
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
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

//download file
if (isset($_GET['doc_id'])) {
    global $conn;

    $UserId = $_SESSION['user_id'];
    $docId = $_GET['doc_id'];

    $query = "SELECT * FROM document WHERE document_id = $docId";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $file = mysqli_fetch_assoc($result);

        $filepath = 'uploads/' . $file['file_path'];

        if (file_exists($filepath)) {
            header('Content-Type: application/octet-stream');
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="' . basename($file['file_name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit();
        } else {
            echo "<script>alert('File not found.');</script>";
        }
    } else {
        echo "<script>alert(Error fetching document from the database.)</script>";
    }
}

//save changes to database
if (isset($_POST['save_changes'])) {
    global $conn;

    $trackId = $_POST['trackid'];
    $comment = $_POST['comment'];
    $bool = isset($_POST['done']) && $_POST['done'] == '1' ? 1 : 0;

    $checkquery = "SELECT document.document_id, status, isAccomplished FROM document JOIN trackdetails ON trackdetails.document_id = document.document_id WHERE trackdetails.track_id = ?";
    $stmt = mysqli_prepare($conn, $checkquery);
    mysqli_stmt_bind_param($stmt, 'i', $trackId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $document_id, $status, $isAccomplished);

    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);

        if ($comment != $status || $bool != $isAccomplished) {
            $updatequery = "UPDATE document SET status = ?, isAccomplished = ? WHERE document_id = ?";
            $updatestmt = mysqli_prepare($conn, $updatequery);
            mysqli_stmt_bind_param($updatestmt, 'sii', $comment, $bool, $document_id);

            if (mysqli_stmt_execute($updatestmt)) {
                echo "<script>alert('Changes are saved!')</script>";
            } else {
                echo "<script>alert('Error saving changes: " . mysqli_error($conn) . "');</script>";
            }

            mysqli_stmt_close($updatestmt);
        } else {
            echo "<script>alert('No changes to save.')</script>";
        }
    } else {
        echo "<script>alert('Error fetching existing data: " . mysqli_error($conn) . "');</script>";
    }
}


if (isset($_POST['delete_inbox'])) {
    $trackId = $_POST['trackid'];

    delete('recipient', $trackId);
}

if (isset($_POST['delete_outbox'])) {
    $trackId = $_POST['trackid'];

    delete('sender', $trackId);
}



?>