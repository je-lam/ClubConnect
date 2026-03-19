<?php

require 'includes/db.php';

$db = get_db();

$page_title = 'Calendar';


$today = new DateTime();
$today_str = $today->format('Y-m-d');
$today_day = (int) $today->format('N'); // 1 = monday, 7 = sunday

// walk back to monday by subtracting however many days we are into the week
$monday = clone $today;
$monday->modify('-' . ($today_day - 1) . ' days');

$week = [];
for ($i = 0; $i < 7; $i++) {
    $day = clone $monday;
    $day->modify('+' . $i . ' days');
    $week[] = [
        'day_of_week' => $i + 1,
        'label' => $day->format('D'),
        'date_num' => $day->format('j'),
        'month_short' => $day->format('M'),
        'is_today' => $day->format('Y-m-d') === $today_str,
        'is_weekend' => $i >= 5
    ];
}


$all_events = $db->query("
    SELECT events.*, clubs.name AS club_name, clubs.logo_url
    FROM events
    JOIN clubs ON clubs.id = events.club_id
    ORDER BY events.start_time
")->fetchAll();

// pre-fill all 7 days so we don't get undefined index errors on empty days
$events_by_day = [1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => []];

foreach ($all_events as $event) {
    $events_by_day[$event['day_of_week']][] = $event;
}

// calendar display range and pixel scale
$start_hour = 8;
$end_hour = 20;
$hour_height = 60; // 1 pixel per minute, so 60px per hour


// converts "14:30" to total minutes (870)
function time_to_minutes($time_str)
{
    $parts = explode(':', $time_str);
    return (int) $parts[0] * 60 + (int) $parts[1];
}

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

// converts an integer hour like 14 to "2 PM" for the sidebar labels
function format_hour($h)
{
    $ampm = $h >= 12 ? 'PM' : 'AM';
    $h12 = $h % 12;
    if ($h12 === 0)
        $h12 = 12;
    return $h12 . ' ' . $ampm;
}

$calendar_start_minutes = $start_hour * 60;
$total_height = ($end_hour - $start_hour) * $hour_height;
$full_width = true;

require 'includes/header.php';
?>

<div class="calendar-wrapper">

    <div class="calendar-heading">
        <?= $today->format('F Y') ?>
    </div>

    <div class="calendar-container">

        <div class="calendar-scroll-area">

            <div class="calendar-header-row">
                <div class="calendar-time-gutter"></div>

                <?php foreach ($week as $day): ?>
                    <div
                        class="calendar-day-header <?= $day['is_today'] ? 'today' : '' ?> <?= $day['is_weekend'] ? 'weekend' : '' ?>">
                        <span class="calendar-day-name"><?= $day['label'] ?></span>
                        <span class="calendar-day-date <?= $day['is_today'] ? 'today-circle' : '' ?>">
                            <?= $day['date_num'] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="calendar-body">

                <div class="calendar-time-col" style="height: <?= $total_height ?>px;">
                    <?php for ($h = $start_hour; $h <= $end_hour; $h++): ?>
                        <?php $top = ($h - $start_hour) * $hour_height; ?>
                        <span class="calendar-time-label" style="top: <?= $top ?>px;">
                            <?= format_hour($h) ?>
                        </span>
                    <?php endfor; ?>
                </div>

                <div class="calendar-days">

                    <div id="current-time-line" class="current-time-line"></div>

                    <?php foreach ($week as $day): ?>
                        <div class="calendar-day-col <?= $day['is_today'] ? 'today' : '' ?> <?= $day['is_weekend'] ? 'weekend' : '' ?>"
                            style="height: <?= $total_height ?>px;">

                            <?php for ($i = 0; $i < ($end_hour - $start_hour); $i++): ?>
                                <div class="calendar-hour-cell"></div>
                            <?php endfor; ?>

                            <?php foreach ($events_by_day[$day['day_of_week']] as $event): ?>
                                <?php
                                $start_min = time_to_minutes($event['start_time']);
                                $end_min = time_to_minutes($event['end_time']);
                                $top = ($start_min - $calendar_start_minutes); // pixels from top of calendar
                                $height = max(24, $end_min - $start_min); // min height so short events are still visible
                                $has_logo = !empty($event['logo_url']);
                                $bg_style = $has_logo
                                    ? " background: linear-gradient(rgba(0,0,0,0.25), rgba(0,0,0,0.25)), url('" . htmlspecialchars($event['logo_url']) . "') center/cover no-repeat;"
                                    : '';
                                ?>
                                <a href="/club.php?id=<?= $event['club_id'] ?>" class="calendar-event<?= $has_logo ? ' calendar-event-has-logo' : '' ?>"
                                    style="top: <?= $top ?>px; height: <?= $height ?>px;<?= $bg_style ?>">
                                    <p class="calendar-event-name"><?= htmlspecialchars($event['club_name']) ?></p>
                                    <p class="calendar-event-time">
                                        <?= format_ampm($event['start_time']) ?> - <?= format_ampm($event['end_time']) ?>
                                    </p>
                                </a>
                            <?php endforeach; ?>

                        </div>
                    <?php endforeach; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require 'includes/footer.php'; ?>