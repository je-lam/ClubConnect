<?php

require 'includes/db.php';

$db = get_db();

$page_title = 'Admin';

$categories = [
    'Academic & Professional',
    'Business',
    'CSO',
    'Cultural',
    'Engineering',
    'Faith-Based',
    'Performance Arts',
    'Recreational',
    'Club Sports',
    'Service & Social Justice',
    'Special Interest',
];

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $logo_url = trim($_POST['logo_url'] ?? '');
        $slack_url = trim($_POST['slack_url'] ?? '');
        $discord_url = trim($_POST['discord_url'] ?? '');
        $instagram_url = trim($_POST['instagram_url'] ?? '');

        if ($name === '' || $category === '') {
            $error_message = 'Name and category are required.';
        } else {
            $stmt = $db->prepare("
                INSERT INTO clubs (name, category, description, logo_url, slack_url, discord_url, instagram_url, featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)
            ");
            $stmt->execute([$name, $category, $description, $logo_url, $slack_url, $discord_url, $instagram_url]);
            $success_message = 'Club "' . htmlspecialchars($name) . '" added successfully.';
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $club_id = filter_var($_POST['club_id'] ?? '', FILTER_VALIDATE_INT);

        if ($club_id) {
            $db->prepare("DELETE FROM events WHERE club_id = ?")->execute([$club_id]);
            $db->prepare("DELETE FROM club_tags WHERE club_id = ?")->execute([$club_id]);
            $db->prepare("DELETE FROM board_members WHERE club_id = ?")->execute([$club_id]);
            $db->prepare("DELETE FROM clubs WHERE id = ?")->execute([$club_id]);
            $success_message = 'Club deleted successfully.';
        }
    }
}

$all_clubs = $db->query("SELECT id, name, category FROM clubs ORDER BY category, name")->fetchAll();

require 'includes/header.php';
?>

<div class="admin-page">

    <?php if ($success_message): ?>
        <p class="admin-success"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <p class="admin-error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>


    <section class="admin-section">
        <h2>Add a Club</h2>

        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="add">

            <label for="name">Club Name *</label>
            <input type="text" id="name" name="name" required>

            <label for="category">Category *</label>
            <select id="category" name="category" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>

            <label for="logo_url">Logo URL</label>
            <input type="text" id="logo_url" name="logo_url" placeholder="https://...">

            <label for="slack_url">Slack URL</label>
            <input type="text" id="slack_url" name="slack_url" placeholder="https://...">

            <label for="discord_url">Discord URL</label>
            <input type="text" id="discord_url" name="discord_url" placeholder="https://...">

            <label for="instagram_url">Instagram URL</label>
            <input type="text" id="instagram_url" name="instagram_url" placeholder="https://...">

            <button type="submit" class="admin-btn-primary">Add Club</button>
        </form>
    </section>


    <section class="admin-section">
        <h2>Remove a Club</h2>
        <p class="admin-note">This also deletes the club's events, tags, and board members.</p>

        <table class="admin-club-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_clubs as $club): ?>
                    <tr>
                        <td><?= htmlspecialchars($club['name']) ?></td>
                        <td><?= htmlspecialchars($club['category']) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($club['name'])) ?>?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="club_id" value="<?= $club['id'] ?>">
                                <button type="submit" class="admin-btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</div>

<?php require 'includes/footer.php'; ?>
