<?php
require_once __DIR__ . '/includes/admin_auth.php';

$product = [
    'id' => null, 'category_id' => '', 'name' => '', 'price' => '', 'unit' => '/pc',
    'old_price' => '', 'badge' => '', 'rating' => 5.0, 'rating_count' => 0,
    'image_url' => '', 'description' => '', 'stock_status' => 'in_stock',
];
$editing = false;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([(int) $_GET['id']]);
    $found = $stmt->fetch();
    if ($found) { $product = $found; $editing = true; }
}

$admin_page_title = $editing ? 'Edit Product' : 'Add Product';
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $unit = trim($_POST['unit'] ?? '/pc');
    $old_price = $_POST['old_price'] !== '' ? (float) $_POST['old_price'] : null;
    $badge = trim($_POST['badge'] ?? '') ?: null;
    $description = trim($_POST['description'] ?? '');
    $stock_status = $_POST['stock_status'] === 'out_of_stock' ? 'out_of_stock' : 'in_stock';
    $image_url = $product['image_url'];

    if ($name === '' || $category_id <= 0 || $price <= 0) {
        $error = 'Name, category, and a valid price are required.';
    } else {
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                $error = 'Image must be jpg, jpeg, png, or webp.';
            } elseif ($_FILES['image']['size'] > 4 * 1024 * 1024) {
                $error = 'Image must be under 4MB.';
            } else {
                $filename = 'product_' . uniqid() . '.' . $ext;
                $dest = __DIR__ . '/../uploads/products/' . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image_url = 'uploads/products/' . $filename;
                } else {
                    $error = 'Failed to upload image. Please try again.';
                }
            }
        } elseif (!$editing) {
            $image_url = 'assets/images/products/placeholder.jpg';
        }

        if ($error === '') {
            if ($editing) {
                $stmt = $pdo->prepare(
                    'UPDATE products SET category_id=?, name=?, price=?, unit=?, old_price=?, badge=?,
                     image_url=?, description=?, stock_status=? WHERE id=?'
                );
                $stmt->execute([$category_id, $name, $price, $unit, $old_price, $badge,
                    $image_url, $description, $stock_status, $product['id']]);
                flash('admin_success', 'Product updated.');
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO products (category_id, name, price, unit, old_price, badge, image_url, description, stock_status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([$category_id, $name, $price, $unit, $old_price, $badge,
                    $image_url, $description, $stock_status]);
                flash('admin_success', 'Product added.');
            }
            redirect('products.php');
        }
    }
}

require __DIR__ . '/includes/admin_header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= clean($error) ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form-grid">
    <div class="admin-card" style="padding:24px;">
        <div class="form-grid">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" value="<?= clean($product['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= (int) $product['category_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= clean($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Price ($)</label>
                <input type="number" step="0.01" min="0.01" name="price" value="<?= clean((string) $product['price']) ?>" required>
            </div>
            <div class="form-group">
                <label>Unit (e.g. /kg, /loaf, /pc)</label>
                <input type="text" name="unit" value="<?= clean($product['unit']) ?>">
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Old Price (optional, for discounts)</label>
                <input type="number" step="0.01" min="0" name="old_price" value="<?= clean((string) ($product['old_price'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label>Badge (e.g. -20%, Best Seller)</label>
                <input type="text" name="badge" value="<?= clean($product['badge'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?= clean($product['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Stock Status</label>
            <select name="stock_status">
                <option value="in_stock" <?= $product['stock_status'] === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
                <option value="out_of_stock" <?= $product['stock_status'] === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
            </select>
        </div>
    </div>

    <div class="admin-card" style="padding:24px;">
        <div class="form-group">
            <label>Product Image</label>
            <?php if (!empty($product['image_url'])): ?>
                <img src="../<?= clean($product['image_url']) ?>" style="width:100%; border-radius:var(--radius-sm); margin-bottom:10px;" onerror="this.style.display='none'">
            <?php endif; ?>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
            <p style="font-size:12px; color:var(--color-text-muted); margin-top:6px;">JPG, PNG, or WEBP. Max 4MB. Leave empty to keep current image.</p>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-2"><?= $editing ? 'Update Product' : 'Add Product' ?></button>
        <a href="products.php" class="btn btn-outline btn-block mt-2">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
