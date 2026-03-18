<?php

require 'includes/db.php';

$db = get_db();

$page_title = 'Home';

require 'includes/header.php';
?>

<div class="welcome-section">
    <h2>Welcome</h2>
    <p class="welcome-description">This is BroncoClubs.</p>
</div>

<div class="today-club-section">
    <h2 align="center">Today's clubs</h2>
</div>

<?php
$today = new DateTime();
?>

<?php require 'includes/footer.php'; ?>