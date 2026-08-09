<?php $settings = get_settings(); ?>
<footer>
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="index.php" class="logo" style="color:#fff;">
                    <?= clean($settings['site_name'] ?? 'FreshMart') ?>
                    <span style="color:var(--color-accent);">.</span>
                </a>
                <p style="margin-top:14px; font-size:14px; max-width:280px;">
                    Your one-stop shop for fresh groceries, fruits, vegetables, bakery items and more —
                    delivered right to your door.
                </p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php#home">Home</a></li>
                    <li><a href="index.php#shop">Shop</a></li>
                    <li><a href="index.php#about">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4>Categories</h4>
                <ul>
                    <li><a href="index.php#shop">Vegetables</a></li>
                    <li><a href="index.php#shop">Fruits</a></li>
                    <li><a href="index.php#shop">Bakery</a></li>
                    <li><a href="index.php#shop">Dairy &amp; Eggs</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact Us</h4>
                <ul>
                    <li><?= clean($settings['address'] ?? '') ?></li>
                    <li><?= clean($settings['hotline'] ?? '') ?></li>
                    <li><?= clean($settings['email'] ?? '') ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> <?= clean($settings['site_name'] ?? 'FreshMart') ?>. All rights reserved.
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
