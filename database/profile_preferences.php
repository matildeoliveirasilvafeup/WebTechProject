<?php
require_once 'connection.php';

function getProfilePreferences(PDO $db, $userId) {

    $stmt = $db->prepare("SELECT * FROM profiles_preferences WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateProfileP(PDO $db, $userId) {

    // TODO //

}
?>