<?php

require 'includes/db.php';

$db = get_db();

$page_title = 'Directory';


$all_clubs = $db->query("
    SELECT * FROM clubs ORDER BY category, name
")->fetchAll();

// fetch all tags in one query then group them by club id
$all_tag_rows = $db->query("SELECT club_id, tag FROM club_tags")->fetchAll();

$tags_by_club = [];
foreach ($all_tag_rows as $row) {
    $tags_by_club[$row['club_id']][] = $row['tag'];
}

// attach the tags array to each club
foreach ($all_clubs as $index => $club) {
    $all_clubs[$index]['tags'] = $tags_by_club[$club['id']] ?? [];
}


// group clubs into buckets by category so we can loop through them in the html
$clubs_by_category = [];

foreach ($all_clubs as $club) {
    $category = $club['category'];
    $clubs_by_category[$category][] = $club;
}

// get the unique list of tags for the filter buttons at the top
$all_tags = $db->query("
    SELECT DISTINCT tag FROM club_tags ORDER BY tag
")->fetchAll(PDO::FETCH_COLUMN);

require 'includes/header.php';
?>

<section class="directory-page">

    <div class="filter-section">
        <h2>Filter Tags</h2>
        <div class="tag-pills">
            <?php foreach ($all_tags as $tag): ?>
                <button class="tag-pill" data-tag="<?= htmlspecialchars($tag) ?>">
                    <?= htmlspecialchars($tag) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>


    <div class="recently-viewed-section">
        <h2>Recently Viewed</h2>
        <div id="recently-viewed-list">
            <p class="no-recent">Nothing viewed yet.</p>
        </div>
    </div>


    <div class="directory-controls">
        <button id="toggle-view-btn">Switch to List View</button>
    </div>


    <?php foreach ($clubs_by_category as $category => $clubs): ?>

        <div class="category-section" data-category="<?= htmlspecialchars($category) ?>">

            <h2 class="category-title"><?= htmlspecialchars($category) ?></h2>

            <div class="club-grid" id="grid-<?= htmlspecialchars($category) ?>">

                <?php foreach ($clubs as $club): ?>

                    <a href="/club.php?id=<?= $club['id'] ?>" class="club-card"
                        data-tags="<?= htmlspecialchars(implode(',', $club['tags'])) ?>" data-id="<?= $club['id'] ?>"
                        data-name="<?= htmlspecialchars($club['name']) ?>">

                        <?php if ($club['logo_url']): ?>
                            <img src="<?= htmlspecialchars($club['logo_url']) ?>" alt="" class="club-card-logo">
                        <?php else: ?>
                            <div class="club-card-logo-placeholder">
                                <?= htmlspecialchars(substr($club['name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>

                        <p class="club-card-name"><?= htmlspecialchars($club['name']) ?></p>

                        <div class="club-card-tags">
                            <?php foreach ($club['tags'] as $tag): ?>
                                <span class="tag-badge"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

        </div>

    <?php endforeach; ?>

</section>

<?php require 'includes/footer.php'; ?>