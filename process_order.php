<?php
require_once __DIR__ . '/config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('order.php');
}

// ---- Validate shipping fields ----
$customer_name   = trim($_POST['customer_name'] ?? '');
$customer_phone  = trim($_POST['customer_phone'] ?? '');
$delivery_address = trim($_POST['delivery_address'] ?? '');
$delivery_instructions = trim($_POST['delivery_instructions'] ?? '');
$payment_method  = $_POST['payment_method'] ?? '';
$cart_json       = $_POST['cart_data'] ?? '[]';

$errors = [];
if ($customer_name === '') $errors[] = 'Full name is required.';
if ($customer_phone === '') $errors[] = 'Phone number is required.';
if ($delivery_address === '') $errors[] = 'Delivery address is required.';
if (!in_array($payment_method, ['MoMo', 'Card', 'COD'], true)) $errors[] = 'Invalid payment method.';

$cart = json_decode($cart_json, true);
if (!is_array($cart) || count($cart) === 0) {
    $errors[] = 'Your cart is empty.';
}

if ($errors) {
    flash('order_error', implode(' ', $errors));
    redirect('order.php');
}

// ---- Compute totals (re-validate item prices against DB to prevent tampering) ----
$delivery_fee = 2.00;
$subtotal = 0;
$validated_items = [];

foreach ($cart as $item) {
    $product_id = (int) ($item['id'] ?? 0);
    $qty = max(1, (int) ($item['qty'] ?? 1));

    $stmt = $pdo->prepare('SELECT id, name, price, image_url FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) continue; // skip items that no longer exist

    $line_subtotal = $product['price'] * $qty;
    $subtotal += $line_subtotal;

    $validated_items[] = [
        'product_id'   => $product['id'],
        'product_name' => $product['name'],
        'quantity'     => $qty,
        'price'        => $product['price'],
        'subtotal'     => $line_subtotal,
    ];
}

if (empty($validated_items)) {
    flash('order_error', 'None of the items in your cart are currently available.');
    redirect('order.php');
}

$total = $subtotal + $delivery_fee;
$tracking_id = generate_tracking_id();

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO orders (user_id, tracking_id, customer_name, customer_phone, delivery_address,
            delivery_instructions, total_amount, payment_method, payment_status, order_status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, "Pending", "Pending")'
    );
    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        $tracking_id,
        $customer_name,
        $customer_phone,
        $delivery_address,
        $delivery_instructions ?: null,
        $total,
        $payment_method,
    ]);
    $order_id = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    foreach ($validated_items as $it) {
        $itemStmt->execute([
            $order_id,
            $it['product_id'],
            $it['product_name'],
            $it['quantity'],
            $it['price'],
            $it['subtotal'],
        ]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    flash('order_error', 'Something went wrong while placing your order. Please try again.');
    redirect('order.php');
}

redirect('order_confirmation.php?tracking_id=' . urlencode($tracking_id));
