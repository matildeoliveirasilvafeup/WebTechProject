<?php
declare(strict_types=1);

function drawSearchForm(string $action = 'search.php', string $placeholder = 'Search services...', bool $isButton = true, string $extraClass = '', 
    string $initialValue = ''): string {
        ob_start();
?>
    <form method="GET" action="<?= htmlspecialchars($action) ?>" class="<?= htmlspecialchars($extraClass) ?>">
        <?php if (!$isButton): ?>
            <span class="search-icon"><i class="fas fa-search"></i></span>
        <?php endif; ?>

        <input type="text" name="q" id="search-service-input" placeholder="<?= htmlspecialchars($placeholder) ?>" 
            value="<?= htmlspecialchars($initialValue) ?>">

        <?php if ($isButton): ?>
            <button type="submit"><i class="fas fa-search"></i></button>
        <?php endif; ?>
    </form>
<?php
    return ob_get_clean();
}
?>