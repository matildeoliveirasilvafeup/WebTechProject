<?php
function renderStars(float $rating): string {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    return str_repeat('⭐', $fullStars) .
           ($halfStar ? '✩' : '') .
           str_repeat('☆', $emptyStars);
}

?>