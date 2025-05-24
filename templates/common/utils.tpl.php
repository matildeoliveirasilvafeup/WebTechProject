<?php function renderUserLink($username, $name) {
    $username = htmlspecialchars($username);
    $name = htmlspecialchars($name);
    return "<a href='/pages/profile.php?user=$username'>$name</a>";
}
?>
