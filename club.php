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

$tags_stmt = $db->prepare("
    SELECT tag
    FROM club_tags
    WHERE club_id = ?
    ORDER BY tag
");
$tags_stmt->execute([$club_id]);
$tags = $tags_stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

$club_category = $club['category'] ?? null;
if ($club_category !== null) {
    // currently, club_tags are populated using the club category,
    // so de-duplicate to avoid showing the same pill twice
    $tags = array_values(array_filter($tags, function ($t) use ($club_category) {
        return $t !== $club_category;
    }));
}

$board_stmt = $db->prepare("
    SELECT name, avatar_url, discord_username
    FROM board_members
    WHERE club_id = ?
    ORDER BY id
");
$board_stmt->execute([$club_id]);
$board_members = $board_stmt->fetchAll() ?: [];

$has_socials =
    !empty($club['slack_url']) ||
    !empty($club['discord_url']) ||
    !empty($club['instagram_url']);

$has_board = count($board_members) > 0;

require 'includes/header.php';
?>

<section class="club-detail-page">
    <div class="club-detail-columns">
        <div class="club-detail-left">
            <h1 class="club-detail-title"><?= htmlspecialchars($club['name'] ?? '') ?></h1>

            <div class="club-detail-meta">
                <?php if (!empty($club['category'])): ?>
                    <span class="club-tag-pill"><?= htmlspecialchars($club['category']) ?></span>
                <?php endif; ?>

                <?php foreach ($tags as $tag): ?>
                    <span class="club-tag-pill"><?= htmlspecialchars($tag) ?></span>
                <?php endforeach; ?>
            </div>

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
            <?php if ($has_socials): ?>
                <div class="club-socials">
                    <h2>Our club's socials</h2>
                    <div class="club-social-icons">
                        <?php if (!empty($club['slack_url'])): ?>
                            <a class="club-social-icon slack" href="<?= htmlspecialchars($club['slack_url']) ?>" target="_blank" rel="noopener noreferrer">Slack</a>
                        <?php endif; ?>
                        <?php if (!empty($club['discord_url'])): ?>
                            <a class="club-social-icon discord" href="<?= htmlspecialchars($club['discord_url']) ?>" target="_blank" rel="noopener noreferrer">Discord</a>
                        <?php endif; ?>
                        <?php if (!empty($club['instagram_url'])): ?>
                            <a class="club-social-icon instagram" href="<?= htmlspecialchars($club['instagram_url']) ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($has_board): ?>
                <div class="club-board-members">
                    <h2>Board Members</h2>
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
                                        <?= htmlspecialchars($initial !== '' ? $initial : 'B') ?>
                                    </div>
                                <?php endif; ?>
                                <p class="board-member-name"><?= htmlspecialchars($member_name) ?></p>
                                <?php if (!empty($member_discord)): ?>
                                    <p class="board-member-discord">@<?= htmlspecialchars($member_discord) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>