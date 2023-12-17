<?php

include('db.php');


session_start();

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


function sendDocument($recipientEmail, $originOffice, $documentData)
{
    global $conn;

    $senderId = $_SESSION['user_id'];

    $recipientQuery = "SELECT personnel_id FROM Personnel WHERE email = '$recipientEmail'";
    $recipientResult = $conn->query($recipientQuery);

    if ($recipientResult->num_rows == 1) {
        $recipientRow = $recipientResult->fetch_assoc();
        $recipientId = $recipientRow['personnel_id'];

        $insertDocumentQuery = "INSERT INTO Document (filename, data, DateCreated, status, isAccomplished) 
                                VALUES ('$documentData', NOW(), 'Pending', FALSE)";
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

function getInboxDocuments()
{
    global $conn;

    $userId = $_SESSION['user_id'];

    $inboxQuery = "SELECT Document.document_id, Document.filename, Document.DateCreated, TrackDetails.origin_office 
                   FROM Document
                   JOIN TrackDetails ON Document.document_id = TrackDetails.document_id
                   JOIN Recipient ON TrackDetails.track_id = Recipient.track_id
                   WHERE Recipient.personnel_id = $userId";

    $result = $conn->query($inboxQuery);

    $inboxDocuments = [];

    while ($row = $result->fetch_assoc()) {
        $inboxDocuments[] = $row;
    }

    return $inboxDocuments;
}

function markDocumentAsAccomplished($documentId, $comments)
{
    global $conn;

    $updateDocumentQuery = "UPDATE Document SET status = 'Accomplished', isAccomplished = TRUE WHERE document_id = $documentId";
    $conn->query($updateDocumentQuery);

    $insertAccomplishmentQuery = "INSERT INTO Accomplishment (document_id, comments) 
                                  VALUES ($documentId, '$comments')";
    $conn->query($insertAccomplishmentQuery);
}

function getOutboxDocuments()
{
    global $conn;

    $userId = $_SESSION['user_id'];

    $outboxQuery = "SELECT Document.document_id, Document.filename, Document.DateCreated, Document.status, Document.isAccomplished,
                    TrackDetails.origin_office, Accomplishment.comments
                    FROM Document
                    JOIN TrackDetails ON Document.document_id = TrackDetails.document_id
                    JOIN Sender ON TrackDetails.track_id = Sender.track_id
                    LEFT JOIN Accomplishment ON Document.document_id = Accomplishment.document_id
                    WHERE Sender.personnel_id = $userId";

    $result = $conn->query($outboxQuery);

    $outboxDocuments = [];

    while ($row = $result->fetch_assoc()) {
        $outboxDocuments[] = $row;
    }

    return $outboxDocuments;
}

?>
