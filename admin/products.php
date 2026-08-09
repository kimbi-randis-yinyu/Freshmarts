<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin_page_title = 'Products';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT image_url FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);

    if ($product && !empty($product['image_url']) && strpos($product['image_url'], 'uploads/') === 0) {
         $path = __DIR__ . '/../' . $product['image_url'];
         if (is_file($path)) @unlink($path);
}
    flash('admin_success', 'Product deleted.');
    redirect('products.php');
}

$products = $pdo->query(
    'SELECT p.*, c.name AS category_name FROM products p
     JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC'
)->fetchAll();

require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-head">
        <h3>All Products (<?= count($products) ?>)</h3>
        <a href="product_form.php" class="btn btn-primary btn-sm">+ Add Product</a>
    </div>
    <table class="admin-table">
        <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if (!$products): ?>
            <tr><td colspan="6" style="text-align:center; color:var(--color-text-muted);">No products yet. Add your first one.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $p): ?>
            <tr>
                <td data-label="Image"><img class="thumb" src="../<?= clean($p['image_url']) ?>" alt="" onerror="this.style.visibility='hidden'"></td>
                <td data-label="Name"><?= clean($p['name']) ?></td>
                <td data-label="Category"><?= clean($p['category_name']) ?></td>
                <td data-label="Price"><?= fmt_price($p['price']) ?> <small style="color:var(--color-text-muted);"><?= clean($p['unit']) ?></small></td>
                <td data-label="Stock">
                    <?php if ($p['stock_status'] === 'in_stock'): ?>
                        <span class="tag-pill tag-completed">In Stock</span>
                    <?php else: ?>
                        <span class="tag-pill tag-cancelled">Out of Stock</span>
                    <?php endif; ?>
                </td>
                <td data-label="Actions" class="action-links">
                    <a href="product_form.php?id=<?= (int) $p['id'] ?>">Edit</a>
                    <a href="products.php?delete=<?= (int) $p['id'] ?>" class="danger"
                       onclick="return confirm('Delete this product? This cannot be undone.');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
