<?php

// Include the database connection file
include('db.php');

// Initialize the session
session_start();

// Function to handle user authentication
function authenticateUser($email, $password)
{
    global $conn;

    // Sanitize user input to prevent SQL injection (you can use prepared statements for better security)
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // SQL query to check user credentials
    $sql = "SELECT personnel_id, name FROM Personnel WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Authentication successful
        $row = $result->fetch_assoc();

        // Set session variables
        $_SESSION['user_id'] = $row['personnel_id'];
        $_SESSION['user_name'] = $row['name'];

        return true;
    } else {
        // Authentication failed
        return false;
    }
}


// Function to send a document
function sendDocument($recipientEmail, $originOffice, $documentData)
{
    global $conn;

    // Get the sender's personnel ID from the session
    $senderId = $_SESSION['user_id'];

    // Check if the recipient exists in the Personnel table
    $recipientQuery = "SELECT personnel_id FROM Personnel WHERE email = '$recipientEmail'";
    $recipientResult = $conn->query($recipientQuery);

    if ($recipientResult->num_rows == 1) {
        $recipientRow = $recipientResult->fetch_assoc();
        $recipientId = $recipientRow['personnel_id'];

        // Insert into Document table
        $insertDocumentQuery = "INSERT INTO Document (filename, data, DateCreated, status, isAccomplished) 
                                VALUES ('$documentData', NOW(), 'Pending', FALSE)";
        $conn->query($insertDocumentQuery);

        $documentId = $conn->insert_id;

        // Insert into TrackDetails table
        $insertTrackDetailsQuery = "INSERT INTO TrackDetails (document_id, origin_office) 
                                    VALUES ($documentId, '$originOffice')";
        $conn->query($insertTrackDetailsQuery);

        // Insert into Sender table
        $insertSenderQuery = "INSERT INTO Sender (track_id, personnel_id) 
                              VALUES ((SELECT track_id FROM TrackDetails WHERE document_id = $documentId), $senderId)";
        $conn->query($insertSenderQuery);

        // Insert into Recipient table
        $insertRecipientQuery = "INSERT INTO Recipient (track_id, personnel_id) 
                                 VALUES ((SELECT track_id FROM TrackDetails WHERE document_id = $documentId), $recipientId)";
        $conn->query($insertRecipientQuery);

        return true;
    } else {
        // Recipient not found
        return false;
    }
}

// Function to retrieve inbox documents for the current user
function getInboxDocuments()
{
    global $conn;

    // Get the current user's personnel ID from the session
    $userId = $_SESSION['user_id'];

    // Query to retrieve inbox documents
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

// Function to mark a document as accomplished and add comments
function markDocumentAsAccomplished($documentId, $comments)
{
    global $conn;

    // Update Document table
    $updateDocumentQuery = "UPDATE Document SET status = 'Accomplished', isAccomplished = TRUE WHERE document_id = $documentId";
    $conn->query($updateDocumentQuery);

    // Insert into Accomplishment table
    $insertAccomplishmentQuery = "INSERT INTO Accomplishment (document_id, comments) 
                                  VALUES ($documentId, '$comments')";
    $conn->query($insertAccomplishmentQuery);
}

// Function to retrieve outbox documents for the current user
function getOutboxDocuments()
{
    global $conn;

    // Get the current user's personnel ID from the session
    $userId = $_SESSION['user_id'];

    // Query to retrieve outbox documents
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
