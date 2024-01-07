<?php

include('db.php');


session_start();

//auth login
function authenticateUser($email, $password)
{
    global $conn;

    $sql = "SELECT personnel_id, name FROM Personnel WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows == 1) {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['user_id'] = $row['personnel_id'];
            $_SESSION['user_name'] = $row['name'];

            mysqli_stmt_close($stmt);

            return true;
        }

        mysqli_stmt_close($stmt);
    }

    return false;
}


//logout user
function logout()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        $_SESSION = array();
        session_destroy();

        header("Location: index.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

//outgoing

//upload
function fileUpload($file)
{
    if ($file['error'] === UPLOAD_ERR_OK) {

        $uploadDirectory = './uploads/';
        $newFile = uniqid() . '_' . basename($file['name']);

        $uploadedFilePath = $uploadDirectory . $newFile ;

        if (move_uploaded_file($file['tmp_name'], $uploadedFilePath)) {
            return $newFile;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//to db
function sendDocument($recipientEmail, $originOffice, $uploadedFilePath, $filename)
{
    global $conn;

    $senderId = $_SESSION['user_id'];

    $recipientQuery = "SELECT personnel_id FROM Personnel WHERE email = ?";
    $stmtRecipient = mysqli_prepare($conn, $recipientQuery);

    if (!$stmtRecipient) {
        echo "<script>alert('Failed to prepare recipient statement.');</script>";
        return false;
    }

    mysqli_stmt_bind_param($stmtRecipient, 's', $recipientEmail);
    mysqli_stmt_execute($stmtRecipient);
    $recipientResult = mysqli_stmt_get_result($stmtRecipient);

    if ($recipientResult->num_rows == 1) {
        $recipientRow = mysqli_fetch_assoc($recipientResult);
        $recipientId = $recipientRow['personnel_id'];

        if ($senderId == $recipientId) {
            echo "<script>alert('You cannot send a document to yourself.');</script>";
            return false;
        }

        $insertDocumentQuery = "INSERT INTO Document (file_path, file_name, DateCreated, isAccomplished) VALUES (?, ?, NOW(), FALSE)";
        $stmtDocument = mysqli_prepare($conn, $insertDocumentQuery);

        if (!$stmtDocument) {
            echo "<script>alert('Failed to prepare document statement.');</script>";
            return false;
        }

        mysqli_stmt_bind_param($stmtDocument, 'ss', $uploadedFilePath, $filename);
        mysqli_stmt_execute($stmtDocument);

        $documentId = mysqli_insert_id($conn);

        $insertTrackDetailsQuery = "INSERT INTO TrackDetails (document_id, origin_office) VALUES (?, ?)";
        $stmtTrackDetails = mysqli_prepare($conn, $insertTrackDetailsQuery);

        if (!$stmtTrackDetails) {
            echo "<script>alert('Failed to prepare track details statement.');</script>";
            return false;
        }

        mysqli_stmt_bind_param($stmtTrackDetails, 'is', $documentId, $originOffice);
        mysqli_stmt_execute($stmtTrackDetails);

        $insertSenderQuery = "INSERT INTO Sender (track_id, personnel_id) VALUES ((SELECT track_id FROM TrackDetails WHERE document_id = ?), ?)";
        $stmtSender = mysqli_prepare($conn, $insertSenderQuery);

        $insertRecipientQuery = "INSERT INTO Recipient (track_id, personnel_id) VALUES ((SELECT track_id FROM TrackDetails WHERE document_id = ?), ?)";
        $stmtRecipient = mysqli_prepare($conn, $insertRecipientQuery);

        if (!$stmtSender || !$stmtRecipient) {
            echo "<script>alert('Failed to prepare sender/recipient statement.');</script>";
            return false;
        }

        mysqli_stmt_bind_param($stmtSender, 'ii', $documentId, $senderId);
        mysqli_stmt_bind_param($stmtRecipient, 'ii', $documentId, $recipientId);

        mysqli_stmt_execute($stmtSender);
        mysqli_stmt_execute($stmtRecipient);

        mysqli_stmt_close($stmtSender);
        mysqli_stmt_close($stmtRecipient);

        echo "<script>alert('Document sent successfully!');</script>";
        return true;
    } else {
        echo "<script>alert('Recipient not found.');</script>";
        return false;
    }
}


//inbox
function getInboxDocuments()
{
    global $conn;

    $userId = $_SESSION['user_id'];

    $inboxQuery = "SELECT recipient.track_id, document.document_id, document.DateCreated, document.file_path, document.file_name, personnel.name, trackdetails.origin_office, document.status, document.isAccomplished
                    FROM Document
                    JOIN trackdetails ON document.document_id = trackdetails.document_id
                    JOIN recipient ON trackdetails.track_id = recipient.track_id
                    JOIN sender ON trackdetails.track_id = sender.track_id
                    JOIN personnel ON sender.personnel_id = personnel.personnel_id
                    WHERE recipient.personnel_id = ?";

    $stmt = mysqli_prepare($conn, $inboxQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $inboxDocuments = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $inboxDocuments[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $inboxDocuments;
    } else {
        echo "<script>alert('Error preparing statement: " . mysqli_error($conn) . "');</script>";
        return [];
    }
}



//outbox
function getOutboxDocuments()
{
    global $conn;

    $userId = $_SESSION['user_id'];

    $outboxQuery = "SELECT sender.track_id, document.document_id, document.DateCreated, document.file_path, document.file_name, personnel.name, trackdetails.origin_office, document.status, document.isAccomplished 
                    FROM Document
                    JOIN trackdetails ON document.document_id = trackdetails.document_id
                    JOIN recipient ON trackdetails.track_id = recipient.track_id
                    JOIN sender ON trackdetails.track_id = sender.track_id
                    JOIN personnel ON recipient.personnel_id = personnel.personnel_id
                    WHERE sender.personnel_id = ?";

    $stmt = mysqli_prepare($conn, $outboxQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $outboxDocuments = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $outboxDocuments[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $outboxDocuments;
    } else {
        echo "<script>alert('Error preparing statement: " . mysqli_error($conn) . "');</script>";
        return [];
    }
}


//delete

function delete($user, $trackid)
{
    global $conn;

    $userId = $_SESSION['user_id'];

    if ($user == 'recipient' || $user == 'sender') {
        $table = ($user == 'recipient') ? 'recipient' : 'sender';

        $query = "DELETE FROM $table WHERE personnel_id = ? AND track_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ii', $userId, $trackid);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Document deleted successfully.')</script>";
            } else {
                echo "<script>alert('Error deleting document: " . mysqli_error($conn) . "');</script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Error preparing statement: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid user type.')</script>";
    }
}


?>