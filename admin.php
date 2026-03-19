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
        $linkedin_url = trim($_POST['linkedin_url'] ?? '');
        $slack_url = trim($_POST['slack_url'] ?? '');
        $discord_url = trim($_POST['discord_url'] ?? '');
        $instagram_url = trim($_POST['instagram_url'] ?? '');

        if ($name === '' || $category === '') {
            $error_message = 'Name and category are required.';
        } else {
            $stmt = $db->prepare("
                INSERT INTO clubs (name, category, description, logo_url, linkedin_url, slack_url, discord_url, instagram_url, featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)
            ");
            $stmt->execute([$name, $category, $description, $logo_url, $linkedin_url, $slack_url, $discord_url, $instagram_url]);
            $success_message = 'Club "' . htmlspecialchars($name) . '" added successfully.';
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $club_id = filter_var($_POST['club_id'] ?? '', FILTER_VALIDATE_INT);
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $logo_url = trim($_POST['logo_url'] ?? '');
        $linkedin_url = trim($_POST['linkedin_url'] ?? '');
        $slack_url = trim($_POST['slack_url'] ?? '');
        $discord_url = trim($_POST['discord_url'] ?? '');
        $instagram_url = trim($_POST['instagram_url'] ?? '');

        if (!$club_id || $name === '' || $category === '') {
            $error_message = 'Name and category are required.';
        } else {
            $stmt = $db->prepare("
                UPDATE clubs
                SET name = ?, category = ?, description = ?, logo_url = ?,
                    linkedin_url = ?, slack_url = ?, discord_url = ?, instagram_url = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $category, $description, $logo_url, $linkedin_url, $slack_url, $discord_url, $instagram_url, $club_id]);
            $success_message = 'Club "' . htmlspecialchars($name) . '" updated successfully.';
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

$edit_club = null;
$edit_id = filter_var($_GET['edit'] ?? '', FILTER_VALIDATE_INT);
if ($edit_id) {
    $stmt = $db->prepare("SELECT * FROM clubs WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_club = $stmt->fetch();
}

require 'includes/header.php';
?>

<div class="admin-page">

    <?php if ($success_message): ?>
        <p class="admin-success"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <p class="admin-error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>


    <?php if ($edit_club): ?>
    <section class="admin-section">
        <h2>Edit Club</h2>

        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="club_id" value="<?= $edit_club['id'] ?>">

            <label for="edit_name">Club Name *</label>
            <input type="text" id="edit_name" name="name" required value="<?= htmlspecialchars($edit_club['name']) ?>">

            <label for="edit_category">Category *</label>
            <select id="edit_category" name="category" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $edit_club['category'] === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="edit_description">Description</label>
            <textarea id="edit_description" name="description" rows="3"><?= htmlspecialchars($edit_club['description'] ?? '') ?></textarea>

            <label for="edit_logo_url">Logo URL</label>
            <input type="text" id="edit_logo_url" name="logo_url" placeholder="https://..." value="<?= htmlspecialchars($edit_club['logo_url'] ?? '') ?>">

            <label for="edit_linkedin_url">LinkedIn URL</label>
            <input type="text" id="edit_linkedin_url" name="linkedin_url" placeholder="https://..." value="<?= htmlspecialchars($edit_club['linkedin_url'] ?? '') ?>">

            <label for="edit_slack_url">Slack URL</label>
            <input type="text" id="edit_slack_url" name="slack_url" placeholder="https://..." value="<?= htmlspecialchars($edit_club['slack_url'] ?? '') ?>">

            <label for="edit_discord_url">Discord URL</label>
            <input type="text" id="edit_discord_url" name="discord_url" placeholder="https://..." value="<?= htmlspecialchars($edit_club['discord_url'] ?? '') ?>">

            <label for="edit_instagram_url">Instagram URL</label>
            <input type="text" id="edit_instagram_url" name="instagram_url" placeholder="https://..." value="<?= htmlspecialchars($edit_club['instagram_url'] ?? '') ?>">

            <div class="admin-edit-actions">
                <button type="submit" class="admin-btn-primary">Save Changes</button>
                <a href="admin.php" class="admin-btn-cancel">Cancel</a>
            </div>
        </form>
    </section>
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

            <label for="linkedin_url">LinkedIn URL</label>
            <input type="text" id="linkedin_url" name="linkedin_url" placeholder="https://...">

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
        <h2>Manage Clubs</h2>
        <p class="admin-note">Edit updates a club's info. Delete also removes its events, tags, and board members.</p>

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
                        <td class="admin-table-actions">
                            <a href="admin.php?edit=<?= $club['id'] ?>" class="admin-btn-edit">Edit</a>
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
