<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin_page_title = 'Categories';

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([(int) $_GET['delete']]);
    flash('admin_success', 'Category deleted.');
    redirect('categories.php');
}

$editing_cat = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editing_cat = $stmt->fetch();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $item_count = (int) ($_POST['item_count'] ?? 0);
    $id = (int) ($_POST['id'] ?? 0);
    $image = trim($_POST['image'] ?? '') ?: 'assets/images/categories/placeholder.jpg';

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        $slug = slugify($name);
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE categories SET name=?, slug=?, image=?, item_count=? WHERE id=?');
            $stmt->execute([$name, $slug, $image, $item_count, $id]);
            flash('admin_success', 'Category updated.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO categories (name, slug, image, item_count) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $slug, $image, $item_count]);
            flash('admin_success', 'Category added.');
        }
        redirect('categories.php');
    }
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= clean($error) ?></div><?php endif; ?>

<div class="admin-form-grid">
    <div class="admin-card">
        <div class="admin-card-head"><h3>All Categories (<?= count($categories) ?>)</h3></div>
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Slug</th><th>Item Count</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= clean($c['name']) ?></td>
                    <td><?= clean($c['slug']) ?></td>
                    <td><?= (int) $c['item_count'] ?></td>
                    <td class="action-links">
                        <a href="categories.php?edit=<?= (int) $c['id'] ?>">Edit</a>
                        <a href="categories.php?delete=<?= (int) $c['id'] ?>" class="danger"
                           onclick="return confirm('Delete this category? Products in it will also be removed.');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-card" style="padding:24px;">
        <h3 style="margin-bottom:14px;"><?= $editing_cat ? 'Edit Category' : 'Add Category' ?></h3>
        <form method="POST">
            <input type="hidden" name="id" value="<?= (int) ($editing_cat['id'] ?? 0) ?>">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?= clean($editing_cat['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Image path or URL</label>
                <input type="text" name="image" value="<?= clean($editing_cat['image'] ?? '') ?>" placeholder="assets/images/categories/example.jpg">
            </div>
            <div class="form-group">
                <label>Item Count</label>
                <input type="number" name="item_count" min="0" value="<?= (int) ($editing_cat['item_count'] ?? 0) ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-block"><?= $editing_cat ? 'Update' : 'Add' ?> Category</button>
            <?php if ($editing_cat): ?><a href="categories.php" class="btn btn-outline btn-block mt-2">Cancel</a><?php endif; ?>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
