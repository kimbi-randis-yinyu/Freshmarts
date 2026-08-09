<?php
require_once __DIR__ . '/config/functions.php';
$page_title = 'Contact Us';
$page_active = 'contact';
$settings = get_settings();
require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-head"><div><span class="section-tag">Get In Touch</span><h2>Contact Us</h2></div></div>

        <div class="order-grid">
            <div class="form-card">
                <form id="contact-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="phone">Phone Number (optional)</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <div class="form-card">
                <h3 style="margin-bottom:16px;">Reach Us Directly</h3>
                <p style="margin-bottom:10px;">📍 <?= clean($settings['address'] ?? '') ?></p>
                <p style="margin-bottom:10px;">📞 <?= clean($settings['hotline'] ?? '') ?></p>
                <p style="margin-bottom:10px;">✉️ <?= clean($settings['email'] ?? '') ?></p>
                <p style="margin-top:20px; color:var(--color-text-muted); font-size:14px;">
                    Our support team typically replies within a few hours during business days.
                </p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
