<?php
if (!isset($page_title)) {
    $page_title = 'ClubConnect';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | ClubConnect</title>
    <link rel="icon" href="/assets/SCU-Seal_Outlined_201-2.jpg">
    <link rel="stylesheet" href="/style.css">
</head>

<body>

    <nav class="navbar">
        <a href="/index.php" class="navbar-logo">
            <img src="/assets/SCU-Seal_Outlined_201-2.jpg" alt="SCU Seal" class="school-logo">
            ClubConnect
        </a>
        <ul class="navbar-links">
            <li><a href="/index.php">Home</a></li>
            <li><a href="/directory.php">Directory</a></li>
            <li><a href="/calendar.php">Calendar</a></li>
        </ul>
        <a href="/admin.php" class="navbar-admin-btn">Admin</a>
    </nav>

    <main class="<?= isset($full_width) ? 'main-full' : '' ?>">