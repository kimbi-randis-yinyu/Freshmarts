# FreshMart — Dynamic PHP/MySQL E-commerce Site

## 1. Setup (XAMPP)

1. Copy the entire `freshmart` folder into your XAMPP `htdocs` directory, e.g.
   `C:\xampp\htdocs\freshmart` (Windows) or `/Applications/XAMPP/htdocs/freshmart` (Mac).
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open `http://localhost/phpmyadmin`, click **Import**, choose `schema.sql`, and run it.
   This creates the `freshmart_db` database, all tables, and some sample categories/products.
4. Visit `http://localhost/freshmart/admin/setup_admin.php` in your browser **once** to create
   your first admin account (name, email, password). This page refuses to run again once an
   admin exists — **delete `admin/setup_admin.php` after use** for security.
5. Visit `http://localhost/freshmart/` to see the storefront, or
   `http://localhost/freshmart/admin/admin_login.php` to sign into the dashboard.

Default DB connection in `config/db.php` assumes XAMPP defaults (`root` / no password).
Change `$DB_USER` / `$DB_PASS` there if your setup differs.

## 2. Folder structure

```
freshmart/
├── schema.sql                 # Import this first
├── config/
│   ├── db.php                 # PDO connection (edit credentials here if needed)
│   └── functions.php          # Shared helpers (session, sanitize, settings, etc.)
├── includes/
│   ├── header.php             # <head> + nav, SEO/OG tags pulled from site_settings
│   ├── footer.php
│   └── product_card.php       # Reusable product card partial
├── index.php                  # Homepage (hero, categories, deals, shop grid, about)
├── order.php / process_order.php / order_confirmation.php / track_order.php
├── contact.php / process_contact.php
├── login.php / register.php / logout.php     # Customer accounts (optional, guest checkout works too)
├── assets/css/style.css       # All site colors live in the :root block at the top
├── assets/js/main.js          # Cart (localStorage), add-to-cart, contact AJAX
├── uploads/products/          # Product images uploaded via admin land here
└── admin/
    ├── setup_admin.php        # Run once, then delete
    ├── admin_login.php
    ├── index.php               # Dashboard (revenue, orders, inquiries, products)
    ├── products.php / product_form.php
    ├── categories.php
    ├── orders.php / order_detail.php
    ├── contact_messages.php
    └── settings.php            # Site name, hotline, favicon, OG image, admin password
```

## 3. How the cart/order flow works

- "Order Now" on a product card stores that item in the browser's `localStorage` and sends
  the shopper straight to `order.php`.
- `order.php` renders the cart from `localStorage`, lets the shopper adjust quantities, fill
  in delivery details, and pick MoMo / Card / COD.
- `process_order.php` **re-validates every price against the database** (never trusts the
  client-side total), inserts the order + line items, and redirects to a confirmation page
  with a tracking ID like `FM-7F2K9A`.
- Anyone can look up an order anytime at `track_order.php` using that tracking ID.

## 4. Re-theming colors

Every color in the site is a CSS variable at the top of `assets/css/style.css`:

```css
:root {
    --color-primary: #2e8b3d;   /* main green */
    --color-accent: #ff8a00;    /* orange accent */
    ...
}
```

Change these to match your exact brand palette and the whole site updates.

## 5. Adding real product photos

Product/category images referenced in `schema.sql` (e.g. `assets/images/products/tomatoes.jpg`)
are placeholders — add real photos at those paths, or just use the admin **Products** page to
upload images per product (they're saved to `uploads/products/`). Until a real image exists,
pages fall back to a stock Unsplash photo automatically so nothing looks broken.

## 6. Security notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt).
- All DB queries use PDO prepared statements — no raw SQL concatenation.
- Order totals are recalculated server-side from the database, never trusted from the client.
- Delete `admin/setup_admin.php` right after creating your admin account.
