<?php
require_once 'connection.php';

function getProfile(PDO $db, $userId) {

    $stmt = $db->prepare("SELECT * FROM profiles WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateProfile(PDO $db, $userId) {

    // TODO //

}
?>