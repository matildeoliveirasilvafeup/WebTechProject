<?php
session_start();
header('Content-Type: application/json');
echo json_encode($_SESSION['user']);
?>

<!-- Example of usage -->
<!-- fetch('get_user.php')
    .then(response => response.json())
    .then(user => {
        console.log("xxx:", user.xxx);
    })
    .catch(error => console.error("ERROR: ", error)); -->