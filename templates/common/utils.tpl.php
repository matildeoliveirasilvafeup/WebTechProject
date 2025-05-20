<?php function renderUserLink($username) {
    $username = htmlspecialchars($username);
    return "<a href='/pages/profile.php?user=$username'>$username</a>";
}
?>