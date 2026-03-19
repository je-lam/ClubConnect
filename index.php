<?php

require 'includes/db.php';

$db = get_db();

$page_title = 'Home';

$today = new DateTime();
$today_day = (int) $today->format('N'); // 1 = monday, 7 = sunday

$stmt = $db->prepare("
    SELECT events.*, clubs.name AS club_name, clubs.logo_url
    FROM events
    JOIN clubs ON clubs.id = events.club_id
    WHERE events.day_of_week = ?
    ORDER BY events.start_time
");

// Get today's club events
$stmt->execute([$today_day]);
$today_events = $stmt->fetchAll();

// converts "14:30" to "2:30 PM"
function format_ampm($time_str)
{
    $parts = explode(':', $time_str);
    $hour = (int) $parts[0];
    $minute = $parts[1];
    $ampm = $hour >= 12 ? 'PM' : 'AM';
    $h12 = $hour % 12;
    if ($h12 === 0)
        $h12 = 12; // 0 would mean 12 in 12 hour time
    return $h12 . ':' . $minute . ' ' . $ampm;
}

require 'includes/header.php';
?>

<div class="welcome-section">
    <h2>Welcome to ClubConnect!</h2>
    <div class="welcome-description">
        <p>
        Looking to get involved on campus outside of class? ClubConnect can help you explore all the clubs at Santa Clara in minutes. No matter your interests, there's a club waiting for you. Here's what you can do on ClubConnect.
        </p>
        <ul class="welcome-list">
            <li>
                <strong>See what clubs have meetings</strong>
                <p class="bullet-point">Check the calendar to plan your week and never miss a meeting. Today's active clubs are below.</p>
            </li>
            <li>
                <strong>Learn more about different clubs</strong>
                <p class="bullet-point">Go to the directory to explore all clubs on campus. Click each club to see if they match your interests.</p>
            </li>
            <li>
                <strong>Access club social media like Instagram, Discord, and Slack</strong>
                <p class="bullet-point">Use the icons on each club's page to connect with members instantly and join conversations.</p>
            </li>
        </ul>
    </div>
    </div>

    <div class="today-club-section">
    <h2>Today's clubs: <?=$today->format('l F j Y') ?> </h2>
    <div class="today-club-body">
        <?php if (!empty($today_events)): ?>
            <?php $index = 0; ?>
            <?php foreach ($today_events as $event): ?>
                    <?php
                        $has_logo = !empty($event['logo_url']);
                        $bg_style = $has_logo
                        ? " background: linear-gradient(rgba(0,0,0,0.25), rgba(0,0,0,0.25)), url('" . htmlspecialchars($event['logo_url']) . "') center/cover no-repeat;"
                        : '';
                    ?>
                <a href="/club.php?id=<?= $event['club_id'] ?>" class="calendar-event<?= $has_logo ? ' calendar-event-has-logo' : '' ?>" style="top: <?= $index * 60 ?>px; <?= $bg_style ?>">
                    <p><?= htmlspecialchars($event['club_name']) ?></p>
                    <p>
                        <?= format_ampm($event['start_time']) ?> -
                        <?= format_ampm($event['end_time']) ?>
                    </p>
                </a>
                <?php $index++; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No club meetings today.</p>
        <?php endif; ?>
    </div>
</div>