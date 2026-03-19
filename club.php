<?php
require 'includes/db.php';

$db = get_db();

$page_title = 'Club';

$club_id = null;
if (isset($_GET['id'])) {
    $club_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
}

if (!$club_id) {
    http_response_code(400);
    echo "Missing or invalid `id` parameter.";
    exit;
}

$club = $db->prepare("
    SELECT *
    FROM clubs
    WHERE id = ?
");
$club->execute([$club_id]);
$club = $club->fetch();

if (!$club) {
    http_response_code(404);
    echo "Club not found.";
    exit;
}

$page_title = $club['name'] ?? $page_title;

$slack_url = trim((string) ($club['slack_url'] ?? ''));
$discord_url = trim((string) ($club['discord_url'] ?? ''));
$instagram_url = trim((string) ($club['instagram_url'] ?? ''));

$board_stmt = $db->prepare("
    SELECT name, avatar_url, discord_username
    FROM board_members
    WHERE club_id = ?
    ORDER BY id
");
$board_stmt->execute([$club_id]);
$board_members = $board_stmt->fetchAll() ?: [];

require 'includes/header.php';
?>

<section class="club-detail-page">
    <div class="club-detail-accent-bar"></div>
    <div class="club-detail-columns">
        <div class="club-detail-left">
            <h1 class="club-detail-title"><?= htmlspecialchars($club['name'] ?? '') ?></h1>

            <div class="club-detail-logo-row">
                <?php if (!empty($club['logo_url'])): ?>
                    <img
                        src="<?= htmlspecialchars($club['logo_url']) ?>"
                        alt=""
                        class="club-detail-logo"
                    >
                <?php else: ?>
                    <div class="club-detail-logo-placeholder">
                        Club Logo
                    </div>
                <?php endif; ?>
            </div>

            <p class="club-detail-description">
                <?= !empty($club['description']) ? htmlspecialchars($club['description']) : 'No description provided.' ?>
            </p>
        </div>

        <div class="club-detail-right">
            <div class="club-socials">
                <h2>Our club's socials</h2>
                <div class="club-social-icons">
                    <?php if ($slack_url !== ''): ?>
                        <a class="club-social-icon" href="<?= htmlspecialchars($slack_url) ?>" target="_blank" rel="noopener noreferrer" aria-label="Slack">
                            <img src="/assets/logolinkedin.png" alt="">
                        </a>
                    <?php else: ?>
                        <span class="club-social-icon club-social-icon-disabled" aria-label="Slack">
                            <img src="/assets/logolinkedin.png" alt="">
                        </span>
                    <?php endif; ?>
                    <?php if ($discord_url !== ''): ?>
                        <a class="club-social-icon" href="<?= htmlspecialchars($discord_url) ?>" target="_blank" rel="noopener noreferrer" aria-label="Discord">
                            <img src="/assets/logodiscord.png" alt="">
                        </a>
                    <?php else: ?>
                        <span class="club-social-icon club-social-icon-disabled" aria-label="Discord">
                            <img src="/assets/logodiscord.png" alt="">
                        </span>
                    <?php endif; ?>
                    <?php if ($instagram_url !== ''): ?>
                        <a class="club-social-icon" href="<?= htmlspecialchars($instagram_url) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <img src="/assets/logoinstagram.png" alt="">
                        </a>
                    <?php else: ?>
                        <span class="club-social-icon club-social-icon-disabled" aria-label="Instagram">
                            <img src="/assets/logoinstagram.png" alt="">
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="club-board-members">
                <h2>Board Members</h2>
                <?php if (count($board_members) > 0): ?>
                    <div class="board-members-grid">
                        <?php foreach ($board_members as $member): ?>
                            <?php
                            $avatar_url = $member['avatar_url'] ?? null;
                            $member_name = $member['name'] ?? '';
                            $member_discord = $member['discord_username'] ?? '';
                            $initial = mb_substr($member_name, 0, 1);
                            ?>
                            <div class="board-member-card">
                                <?php if (!empty($avatar_url)): ?>
                                    <img src="<?= htmlspecialchars($avatar_url) ?>" alt="" class="board-member-avatar">
                                <?php else: ?>
                                    <div class="board-member-avatar board-member-avatar-placeholder">
                                        <?= htmlspecialchars($initial !== '' ? $initial : '?') ?>
                                    </div>
                                <?php endif; ?>
                                <p class="board-member-name"><?= htmlspecialchars($member_name) ?></p>
                                <?php if (!empty($member_discord)): ?>
                                    <p class="board-member-discord">@<?= htmlspecialchars($member_discord) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>