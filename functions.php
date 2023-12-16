<?php

include 'db.php';

function createDocument($status_id, $content, $origin_office, $target_recipient) {
    global $conn;

    $date_created = date("Y-m-d H:i:s");
    $last_modified = date("Y-m-d H:i:s");

    $sql = "INSERT INTO Document (status_id, content, date_created, last_modified, origin_office, target_recipient)
            VALUES ('$status_id', '$content', '$date_created', '$last_modified', '$origin_office', '$target_recipient')";

    return $conn->query($sql);
}

function getDocuments() {
    global $conn;

    $sql = "SELECT * FROM Document";
    $result = $conn->query($sql);

    return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : array();
}

function updateDocument($document_id, $status_id, $content, $origin_office, $target_recipient) {
    global $conn;

    $last_modified = date("Y-m-d H:i:s");

    $sql = "UPDATE Document
            SET status_id = '$status_id', content = '$content', last_modified = '$last_modified',
                origin_office = '$origin_office', target_recipient = '$target_recipient'
            WHERE document_id = $document_id";

    return $conn->query($sql);
}

function deleteDocument($document_id) {
    global $conn;

    $sql = "DELETE FROM Document WHERE document_id = $document_id";

    return $conn->query($sql);
}

$conn->close();

?>
