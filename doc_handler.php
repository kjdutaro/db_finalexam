<?php

include('db.php');


session_start();

//auth login goods na
function authenticateUser($email, $password)
{
    global $conn;

    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "SELECT personnel_id, name FROM Personnel WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $_SESSION['user_id'] = $row['personnel_id'];
        $_SESSION['user_name'] = $row['name'];

        return true;
    } else {
        return false;
    }
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

//outgoing goods na

//upload
function fileUpload($file)
{
    if ($file['error'] === UPLOAD_ERR_OK) {

        $uploadDirectory = './uploads/';

        $uploadedFilePath = uniqid() . '_' . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $uploadedFilePath)) {
            return $uploadedFilePath;
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

    $recipientQuery = "SELECT personnel_id FROM Personnel WHERE email = '$recipientEmail'";
    $recipientResult = $conn->query($recipientQuery);

    if ($recipientResult->num_rows == 1) {
        $recipientRow = $recipientResult->fetch_assoc();
        $recipientId = $recipientRow['personnel_id'];

        $insertDocumentQuery = "INSERT INTO Document (file_path, file_name, DateCreated, isAccomplished) 
                                VALUES ('$uploadedFilePath', '$filename', NOW(), FALSE)";
        $conn->query($insertDocumentQuery);

        $documentId = $conn->insert_id;

        $insertTrackDetailsQuery = "INSERT INTO TrackDetails (document_id, origin_office) 
                                    VALUES ($documentId, '$originOffice')";
        $conn->query($insertTrackDetailsQuery);

        $insertSenderQuery = "INSERT INTO Sender (track_id, personnel_id) 
                              VALUES ((SELECT track_id FROM TrackDetails WHERE document_id = $documentId), $senderId)";
        $conn->query($insertSenderQuery);

        $insertRecipientQuery = "INSERT INTO Recipient (track_id, personnel_id) 
                                 VALUES ((SELECT track_id FROM TrackDetails WHERE document_id = $documentId), $recipientId)";
        $conn->query($insertRecipientQuery);

        return true;
    } else {
        return false;
    }
}


//inbox goods na ni
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
    WHERE recipient.personnel_id = $userId";

    $result = $conn->query($inboxQuery);

    $inboxDocuments = [];

    while ($row = $result->fetch_assoc()) {
        $inboxDocuments[] = $row;
    }

    return $inboxDocuments;
}


//status inbox waz pa



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
    WHERE sender.personnel_id = $userId";

    $result = $conn->query($outboxQuery);

    $outboxDocuments = [];

    while ($row = $result->fetch_assoc()) {
        $outboxDocuments[] = $row;
    }

    return $outboxDocuments;
}


