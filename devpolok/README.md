# 🚀 DevPolok Portfolio CMS — Setup Guide

## 📁 Folder Structure

```
devpolok/
├── index.php              ← Main portfolio homepage
├── config.php             ← Database config (EDIT THIS)
├── api.php                ← Contact form API
├── database.sql           ← Database schema + seed data
├── .htaccess              ← Security & performance rules
│
├── admin/
│   ├── login.php          ← Admin login
│   ├── logout.php
│   ├── index.php          ← Dashboard
│   ├── settings.php       ← Site settings (hero, about, SEO, colors...)
│   ├── menu.php           ← Navigation manager
│   ├── skills.php         ← Skills manager
│   ├── services.php       ← Services manager
│   ├── projects.php       ← Projects manager
│   ├── testimonials.php   ← Testimonials manager
│   ├── messages.php       ← Contact messages inbox
│   ├── profile.php        ← Admin profile & password
│   ├── assets/
│   │   └── admin.css
│   └── includes/
│       ├── header.php
│       └── footer.php
│
└── assets/
    ├── css/style.css      ← Main stylesheet
    ├── js/main.js         ← Main JavaScript
    └── uploads/           ← All uploaded images stored here
        ├── projects/
        ├── testimonials/
        ├── logo/
        └── about/
```

---

## ⚙️ Installation Steps (XAMPP)

### Step 1 — Install XAMPP
Download and install XAMPP from: https://www.apachefriends.org/

### Step 2 — Copy Project Files
```
Copy the `devpolok` folder to:
C:\xampp\htdocs\devpolok\
```

### Step 3 — Create Database
1. Open XAMPP Control Panel → Start **Apache** and **MySQL**
2. Open browser → go to `http://localhost/phpmyadmin`
3. Click **New** → Create database named: `devpolok_portfolio`
4. Select the new database → click **Import**
5. Choose the file: `devpolok/database.sql` → Click **Go**

### Step 4 — Configure Database
Open `config.php` and edit:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Your MySQL username
define('DB_PASS', '');           // Your MySQL password (blank for XAMPP default)
define('DB_NAME', 'devpolok_portfolio');
define('SITE_URL', 'http://localhost/devpolok');  // Your URL
```

### Step 5 — Set Folder Permissions
Make sure these folders are writable:
```
devpolok/assets/uploads/
devpolok/assets/uploads/projects/
devpolok/assets/uploads/testimonials/
devpolok/assets/uploads/logo/
devpolok/assets/uploads/about/
```
On Windows (XAMPP), these are writable by default.
On Linux: `chmod -R 755 assets/uploads/`

### Step 6 — Access the Site
- **Portfolio:** `http://localhost/devpolok/`
- **Admin Panel:** `http://localhost/devpolok/admin/`

### Step 7 — Login to Admin
```
Username: admin
Password: password
```
⚠️ **Change your password immediately** after first login!  
Go to: Admin Panel → My Profile → Change Password

---

## 🔐 Admin Panel Features

| Page | Features |
|------|----------|
| **Dashboard** | Stats overview, quick actions, recent messages |
| **Settings** | Hero text, About bio, Contact info, Social links, Colors, SEO, Logo/Favicon |
| **Menu Manager** | Add/Edit/Delete nav items, drag to reorder, toggle visibility |
| **Skills** | Add skills with percentage bars and category grouping |
| **Services** | Add services with icons and descriptions |
| **Projects** | Add projects with images, tech stack, live/GitHub links, featured flag |
| **Testimonials** | Add client reviews with photo, rating, and details |
| **Messages** | View contact form submissions, read/delete, reply via email |
| **My Profile** | Update username/email, change password |

---

## 🌐 Live Server Deployment

### Upload files to server
```bash
# Upload devpolok/ folder to your public_html or www directory
```

### Update config.php for live server
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_cpanel_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');
define('SITE_URL', 'https://yourdomain.com');  // No trailing slash
```

### Enable HTTPS redirect in .htaccess
Uncomment these lines in `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 🛠️ Customization Tips

### Change Default Admin Password
The SQL file uses a hashed password. To change it:
1. Login to admin panel
2. Go to **My Profile** → **Change Password**

Or update directly in database:
```php
// Generate hash in PHP:
echo password_hash('your_new_password', PASSWORD_DEFAULT);
```

### Add Google Analytics
Add your GA script in `index.php` before the closing `</head>` tag.

### Custom Domain Email
To send email notifications when a message arrives, add to `api.php`:
```php
mail($adminEmail, 'New Contact Message', $message, "From: $email");
```

---

## 🔒 Security Features Included
- ✅ PDO prepared statements (SQL injection prevention)
- ✅ `htmlspecialchars()` output sanitization (XSS prevention)
- ✅ `password_hash()` / `password_verify()` for admin login
- ✅ Rate limiting on contact form (3 per hour per IP)
- ✅ File type validation on uploads
- ✅ PHP execution blocked in uploads directory
- ✅ Directory listing disabled via `.htaccess`
- ✅ Session-based admin authentication

---

## 📞 Support
Built by **Hasibul Polok (DevPolok)**
- GitHub: https://github.com/hasibulpolok
- Fiverr: https://www.fiverr.com/developerpolok
- Email: hasibulpolok.bdn@gmail.com
