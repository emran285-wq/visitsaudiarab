# VisitSaudiArab — cPanel Git Version Control Deployment Guide

This document explains how to deploy the VisitSaudiArab WordPress theme and
must-use plugin from GitHub to a cPanel shared-hosting account.

**Key idea:** GitHub stores the source code. cPanel pulls that source into a
separate directory **outside** `public_html`. A small deployment script then
copies only the theme and plugin files into the live WordPress document root.
Uploads, logs, `wp-config.php`, and the `.env` file stay untouched.

---

## 1. Values you must have before starting

- Your **cPanel username** (e.g. `yourcpaneluser`).
- Your **document root** path:
  - Primary domain: `/home/yourcpaneluser/public_html`
  - Addon domain: `/home/yourcpaneluser/public_html/example.com`
- Your **production domain** (e.g. `https://visitsaudiarab.com`).
- A **MySQL database** already created in cPanel, with:
  - database name
  - database user
  - strong password
  - host (almost always `localhost` on cPanel)
- WordPress **salts** from https://api.wordpress.org/secret-key/1.1/salt/

---

## 2. First deployment (initial setup)

### 2.1 Create the GitHub repository

1. Go to GitHub and create a new private repository, e.g. `VisitSaudiArab`.
2. Do **not** initialize it with a README or .gitignore; this project already
   contains those files.
3. Copy the repository URL (HTTPS or SSH). Example:
   `https://github.com/YOUR_USERNAME/VisitSaudiArab.git`

### 2.2 Push this project to GitHub

From your local project folder run:

```bash
# Replace with your real GitHub repository URL
git init -b main
git add .
git commit -m "Initial commit: VisitSaudiArab theme + mu-plugin + deployment files"
git remote add origin https://github.com/YOUR_USERNAME/VisitSaudiArab.git
git push -u origin main
```

### 2.3 Install WordPress on cPanel

Choose **one** method:

**Method A — cPanel Softaculous (easiest):**

1. Log in to cPanel.
2. Open **WordPress** under Softaculous Apps Installer.
3. Choose your domain and directory (usually leave directory blank for primary
   domain).
4. Set admin username, password, and email.
5. Finish installation.

**Method B — Manual WordPress install:**

1. Download WordPress from https://wordpress.org/download/.
2. Upload and extract it into `public_html` using cPanel File Manager.
3. Run the installer by visiting your domain.

### 2.4 Configure `wp-config.php` to use the environment file

1. In cPanel File Manager, open `public_html/wp-config.php`.
2. Add the following line at the **very top**, before any `define()` calls:

   ```php
   <?php
   require_once __DIR__ . '/wp-content/mu-plugins/vsa-env-loader.php';
   ```

3. Change the database defines to use environment values:

   ```php
   define( 'DB_NAME',     getenv( 'DB_NAME' )     ?: 'your_database_name' );
   define( 'DB_USER',     getenv( 'DB_USER' )     ?: 'your_database_user' );
   define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) ?: 'your_database_password' );
   define( 'DB_HOST',     getenv( 'DB_HOST' )     ?: 'localhost' );
   define( 'DB_CHARSET',  getenv( 'DB_CHARSET' )  ?: 'utf8mb4' );
   define( 'DB_COLLATE',  getenv( 'DB_COLLATE' )  ?: '' );
   ```

4. Change the site URL defines:

   ```php
   define( 'WP_HOME',    getenv( 'WP_HOME' )    ?: 'https://visitsaudiarab.com' );
   define( 'WP_SITEURL', getenv( 'WP_SITEURL' ) ?: 'https://visitsaudiarab.com' );
   ```

5. Change the debug defines so errors are logged but never shown to visitors:

   ```php
   define( 'WP_DEBUG',         filter_var( getenv( 'WP_DEBUG' ) ?: false, FILTER_VALIDATE_BOOL ) );
   define( 'WP_DEBUG_LOG',     filter_var( getenv( 'WP_DEBUG_LOG' ) ?: false, FILTER_VALIDATE_BOOL ) );
   define( 'WP_DEBUG_DISPLAY', false );
   @ini_set( 'display_errors', 'Off' );
   ```

6. Keep the WordPress salt lines or replace them with:

   ```php
   define( 'AUTH_KEY',        getenv( 'AUTH_KEY' )        ?: 'put your unique phrase here' );
   define( 'SECURE_AUTH_KEY', getenv( 'SECURE_AUTH_KEY' ) ?: 'put your unique phrase here' );
   define( 'LOGGED_IN_KEY',   getenv( 'LOGGED_IN_KEY' )   ?: 'put your unique phrase here' );
   define( 'NONCE_KEY',       getenv( 'NONCE_KEY' )       ?: 'put your unique phrase here' );
   define( 'AUTH_SALT',       getenv( 'AUTH_SALT' )       ?: 'put your unique phrase here' );
   define( 'SECURE_AUTH_SALT', getenv( 'SECURE_AUTH_SALT' ) ?: 'put your unique phrase here' );
   define( 'LOGGED_IN_SALT',  getenv( 'LOGGED_IN_SALT' )  ?: 'put your unique phrase here' );
   define( 'NONCE_SALT',      getenv( 'NONCE_SALT' )      ?: 'put your unique phrase here' );
   ```

   Use real salts from https://api.wordpress.org/secret-key/1.1/salt/.

### 2.5 Create the `.env` file outside `public_html`

1. In cPanel File Manager, create a folder outside `public_html`, for example:
   `/home/yourcpaneluser/private/`
2. Create a new file named `visitsaudiarab.env`.
3. Copy the contents of `.env.example` from this repository and fill in your
   real values.
4. Save the file and set its permissions to **600** (readable/writable by owner
   only).

Example:

```ini
DB_NAME=your_database_name
DB_USER=your_database_username
DB_PASSWORD=your_strong_database_password
DB_HOST=localhost
DB_CHARSET=utf8mb4
DB_COLLATE=utf8mb4_unicode_ci

WP_HOME=https://visitsaudiarab.com
WP_SITEURL=https://visitsaudiarab.com

WP_ENV=production
WP_DEBUG=false
WP_DEBUG_LOG=false
WP_DEBUG_DISPLAY=false

# Replace with real salts
AUTH_KEY=your_unique_salt
SECURE_AUTH_KEY=your_unique_salt
LOGGED_IN_KEY=your_unique_salt
NONCE_KEY=your_unique_salt
AUTH_SALT=your_unique_salt
SECURE_AUTH_SALT=your_unique_salt
LOGGED_IN_SALT=your_unique_salt
NONCE_SALT=your_unique_salt
```

### 2.6 Update the deploy script

1. In your GitHub repository, open `deploy/cpanel-deploy.sh`.
2. Find the line:

   ```bash
   DEST_DIR="${DEPLOY_DEST:-/home/YOUR_CPANEL_USER/public_html}"
   ```

3. Replace `/home/YOUR_CPANEL_USER/public_html` with your real document root.
   Example for an addon domain:

   ```bash
   DEST_DIR="${DEPLOY_DEST:-/home/yourcpaneluser/public_html/visitsaudiarab.com}"
   ```

4. Commit and push the change:

   ```bash
   git add deploy/cpanel-deploy.sh
   git commit -m "Set cPanel deployment destination"
   git push origin main
   ```

### 2.7 Connect GitHub to cPanel Git Version Control

1. Log in to cPanel.
2. Open **Git Version Control**.
3. Click **Create**.
4. Toggle **Clone a Repository**.
5. Enter your repository URL: `https://github.com/YOUR_USERNAME/VisitSaudiArab.git`
6. Enter a Repository Path outside `public_html`, for example:
   `/home/yourcpaneluser/repositories/VisitSaudiArab`
7. Enter a Repository Name, e.g. `VisitSaudiArab`.
8. Choose the branch to deploy, usually `main`.
9. Click **Create**.

cPanel will clone the repository. If `.cpanel.yml` is present, it will run the
deploy script automatically.

### 2.8 Check the first deployment

1. In cPanel File Manager, verify these paths exist:
   - `public_html/wp-content/themes/visitsaudiarab/`
   - `public_html/wp-content/mu-plugins/visitsaudiarab-core.php`
   - `public_html/wp-content/mu-plugins/vsa-env-loader.php`
2. If the deployment failed, check the Git Version Control logs in cPanel.

### 2.9 Merge `.htaccess` rules

1. Open `public_html/.htaccess` in cPanel File Manager.
2. If there is no `.htaccess` file, create one.
3. Copy the contents of `htaccess-additions.txt` from this repository and paste
   them **above** the `# BEGIN WordPress` block.
4. Save.

### 2.10 Update `robots.txt` domain

1. Open `public_html/robots.txt`.
2. If the Sitemap line uses the wrong domain, update it:

   ```
   Sitemap: https://visitsaudiarab.com/sitemap_index.xml
   ```

### 2.11 Activate the theme and flush permalinks

1. Log in to `https://visitsaudiarab.com/wp-admin/`.
2. Go to **Appearance → Themes**.
3. Activate **VisitSaudiArab**.
4. Go to **Settings → Permalinks**.
5. Choose **Post name** (`/%postname%/`) and click **Save Changes**.

The first deployment is now complete.

---

## 3. Later updates

After the first deployment, updating the live site is simple:

1. Make changes locally.
2. Commit and push to the same branch that cPanel is tracking:

   ```bash
   git add .
   git commit -m "Describe your change"
   git push origin main
   ```

3. cPanel Git Version Control will automatically pull the new commit and run
   `deploy/cpanel-deploy.sh`.
4. The script copies updated theme and plugin files into `public_html`.

**Pull vs. Deploy:**

- **Pull** = cPanel downloads the latest commit from GitHub into the repository
  directory outside `public_html`.
- **Deploy** = the `.cpanel.yml` deploy script copies the relevant files from
  that repository directory into the live `public_html` directory.

### Manual pull from cPanel

If you ever want to pull manually instead of waiting for automatic deployment:

1. cPanel → **Git Version Control**.
2. Find your repository.
3. Click **Update from Remote** or **Pull**.

---

## 4. Backup and rollback

### Before every update

1. Back up the database:
   - cPanel → **Backup Wizard** or **phpMyAdmin** → Export.
2. Back up the current theme and plugin:
   - In File Manager, zip `wp-content/themes/visitsaudiarab/` and
     `wp-content/mu-plugins/`.
   - Download the zip to your computer.

### Rollback after a bad deploy

**Option A — Revert the Git commit:**

1. On your local machine, revert the bad commit and push:

   ```bash
   git revert HEAD
   git push origin main
   ```

2. cPanel will pull and re-deploy the previous version.

**Option B — Restore from zip:**

1. In cPanel File Manager, delete or rename the broken theme/plugin folders.
2. Upload your backup zip.
3. Extract it into the correct `wp-content/` location.

**Database rollback:**

1. In phpMyAdmin, drop all tables or delete the database.
2. Re-import your backup `.sql` file.

---

## 5. Security checklist

- [ ] `wp-config.php` is not inside this Git repository.
- [ ] `.env` file is outside `public_html` with permissions `600`.
- [ ] `WP_DEBUG_DISPLAY` is `false` on production.
- [ ] HTTPS is forced via `.htaccess`.
- [ ] Directory listing is disabled (`Options -Indexes`).
- [ ] Security headers are present in `.htaccess`.
- [ ] WordPress admin password is strong and 2FA is enabled.
- [ ] `wp-content/uploads/` is writable but not executable.

---

## 6. Troubleshooting

### "Destination directory does not exist"

Open `deploy/cpanel-deploy.sh` and set `DEST_DIR` to the exact document root
shown in cPanel → Domains → Document Root.

### "Does not appear to be a WordPress document root"

The deploy script checks for `wp-config.php` or `wp-load.php`. Install WordPress
first, then re-run the deployment.

### Site shows database connection error

1. Check that `vsa-env-loader.php` exists in `wp-content/mu-plugins/`.
2. Check that the `.env` file exists outside `public_html`.
3. Check that `wp-config.php` uses `getenv()`.
4. Verify database credentials in cPanel → MySQL Databases.

### Changes not showing after deploy

1. Clear any cache plugin cache.
2. Clear server/LiteSpeed cache.
3. Clear Cloudflare cache if using Cloudflare.
4. Confirm the commit was pushed to the branch cPanel is tracking.

### 500 Internal Server Error

1. Check `wp-content/debug.log`.
2. Check cPanel error logs.
3. Verify PHP version is 8.1 or newer.
4. Temporarily set `WP_DEBUG=true` and `WP_DEBUG_DISPLAY=true` only while
   debugging locally or via IP; never leave `WP_DEBUG_DISPLAY=true` on a public
   production site.

---

# বাংলা নির্দেশনা — cPanel Git Version Control দিয়ে ডিপ্লয়

## গুরুত্বপূর্ণ ধারণা

GitHub-এ শুধু সোর্স কোড থাকবে। cPanel সেই কোড `public_html`-এর বাইরে আলাদা একটি
ফোল্ডারে নামাবে। তারপর একটি ছোট স্ক্রিপ্ট (`deploy/cpanel-deploy.sh`) শুধু theme
এবং plugin ফাইলগুলো লাইভ `public_html`-এ কপি করবে। Uploads, logs,
`wp-config.php`, এবং `.env` ফাইল কখনোই পরিবর্তন হবে না।

## প্রথম ডিপ্লয়মেন্ট

### ধাপ ১ — GitHub রিপোজিটরি তৈরি করুন

1. GitHub-এ গিয়ে একটি নতুন **private** রিপোজিটরি তৈরি করুন, যেমন `VisitSaudiArab`।
2. README বা .gitignore দিয়ে শুরু করবেন না; এই প্রোজেক্টে সেগুলো আছে।
3. রিপোজিটরি URL কপি করুন।
   উদাহরণ: `https://github.com/YOUR_USERNAME/VisitSaudiArab.git`

### ধাপ ২ — লোকাল প্রোজেক্ট GitHub-এ পুশ করুন

```bash
git init -b main
git add .
git commit -m "Initial commit: VisitSaudiArab theme + mu-plugin + deployment files"
git remote add origin https://github.com/YOUR_USERNAME/VisitSaudiArab.git
git push -u origin main
```

### ধাপ ৩ — cPanel-এ WordPress ইনস্টল করুন

**সহজ উপায় — Softaculous:**

1. cPanel-এ লগইন করুন।
2. **WordPress** খুলুন (Softaculous Apps Installer-এর নিচে)।
3. আপনার ডোমেইন নির্বাচন করুন।
4. Admin username, password, email দিন।
5. Install ক্লিক করুন।

### ধাপ ৪ — `wp-config.php` কনফিগার করুন

1. cPanel File Manager-এ `public_html/wp-config.php` খুলুন।
2. সবার উপরে এই লাইনটি যোগ করুন, যেকোনো `define()`-এর আগে:

   ```php
   <?php
   require_once __DIR__ . '/wp-content/mu-plugins/vsa-env-loader.php';
   ```

3. ডাটাবেস সেটিংস পরিবর্তন করুন:

   ```php
   define( 'DB_NAME',     getenv( 'DB_NAME' )     ?: 'your_database_name' );
   define( 'DB_USER',     getenv( 'DB_USER' )     ?: 'your_database_user' );
   define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) ?: 'your_database_password' );
   define( 'DB_HOST',     getenv( 'DB_HOST' )     ?: 'localhost' );
   define( 'DB_CHARSET',  getenv( 'DB_CHARSET' )  ?: 'utf8mb4' );
   define( 'DB_COLLATE',  getenv( 'DB_COLLATE' )  ?: '' );
   ```

4. সাইট URL সেটিংস:

   ```php
   define( 'WP_HOME',    getenv( 'WP_HOME' )    ?: 'https://visitsaudiarab.com' );
   define( 'WP_SITEURL', getenv( 'WP_SITEURL' ) ?: 'https://visitsaudiarab.com' );
   ```

5. ডিবাগ সেটিংস (প্রোডাকশনে এরর দেখানো বন্ধ):

   ```php
   define( 'WP_DEBUG',         filter_var( getenv( 'WP_DEBUG' ) ?: false, FILTER_VALIDATE_BOOL ) );
   define( 'WP_DEBUG_LOG',     filter_var( getenv( 'WP_DEBUG_LOG' ) ?: false, FILTER_VALIDATE_BOOL ) );
   define( 'WP_DEBUG_DISPLAY', false );
   @ini_set( 'display_errors', 'Off' );
   ```

6. Salt লাইনগুলো https://api.wordpress.org/secret-key/1.1/salt/ থেকে নিয়ে বসান।

### ধাপ ৫ — `.env` ফাইল তৈরি করুন (public_html-এর বাইরে)

1. cPanel File Manager-এ `public_html`-এর বাইরে একটি ফোল্ডার তৈরি করুন, যেমন
   `/home/yourcpaneluser/private/`
2. সেখানে `visitsaudiarab.env` নামে একটি ফাইল তৈরি করুন।
3. রিপোজিটরির `.env.example` ফাইলের কন্টেন্ট কপি করে আসল ভ্যালু দিয়ে পূরণ করুন।
4. ফাইলের permission `600` করে দিন (শুধু owner পড়তে পারবে)।

### ধাপ ৬ — ডিপ্লয় স্ক্রিপ্ট আপডেট করুন

1. GitHub-ে `deploy/cpanel-deploy.sh` খুলুন।
2. এই লাইনটি খুঁজুন:

   ```bash
   DEST_DIR="${DEPLOY_DEST:-/home/YOUR_CPANEL_USER/public_html}"
   ```

3. আপনার আসল document root দিয়ে পরিবর্তন করুন:

   ```bash
   DEST_DIR="${DEPLOY_DEST:-/home/yourcpaneluser/public_html}"
   ```

4. Commit এবং push করুন:

   ```bash
   git add deploy/cpanel-deploy.sh
   git commit -m "Set cPanel deployment destination"
   git push origin main
   ```

### ধাপ ৭ — cPanel-এ Git Version Control কানেক্ট করুন

1. cPanel-এ **Git Version Control** খুলুন।
2. **Create** ক্লিক করুন।
3. **Clone a Repository** অন করুন।
4. রিপোজিটরি URL দিন।
5. Repository Path হিসেবে `public_html`-এর বাইরে রাখুন:
   `/home/yourcpaneluser/repositories/VisitSaudiArab`
6. Branch `main` রাখুন।
7. **Create** ক্লিক করুন।

cPanel রিপোজিটরি ক্লোন করবে এবং `.cpanel.yml` অনুযায়ী ডিপ্লয়মেন্ট চালাবে।

### ধাপ ৮ — প্রথম ডিপ্লয় চেক করুন

File Manager-ে যাচাই করুন:

- `public_html/wp-content/themes/visitsaudiarab/` আছে কিনা
- `public_html/wp-content/mu-plugins/visitsaudiarab-core.php` আছে কিনা
- `public_html/wp-content/mu-plugins/vsa-env-loader.php` আছে কিনা

### ধাপ ৯ — `.htaccess`-এ নিয়ম যোগ করুন

1. `public_html/.htaccess` খুলুন।
2. `htaccess-additions.txt`-এর কন্টেন্ট `# BEGIN WordPress`-এর উপরে বসান।

### ধাপ ১০ — Theme অ্যাক্টিভেট এবং Permalink ফ্লাশ করুন

1. `https://visitsaudiarab.com/wp-admin/`-এ লগইন করুন।
2. **Appearance → Themes** → **VisitSaudiArab** অ্যাক্টিভেট করুন।
3. **Settings → Permalinks** → **Post name** নির্বাচন করে **Save Changes** ক্লিক করুন।

প্রথম ডিপ্লয়মেন্ট শেষ।

---

## পরবর্তী আপডেট

প্রতিবার কোড পরিবর্তনের পর:

```bash
git add .
git commit -m "আপনার পরিবর্তনের বর্ণনা"
git push origin main
```

cPanel স্বয়ংক্রিয়ভাবে পুল করে নতুন কোড ডিপ্লয় করবে।

**Pull বনাম Deploy:**

- **Pull** = cPanel GitHub থেকে কোড `public_html`-এর বাইরের রিপোজিটরি ফোল্ডারে আনে।
- **Deploy** = `.cpanel.yml` অনুযায়ী স্ক্রিপ্ট সেই ফোল্ডার থেকে লাইভ `public_html`-এ কপি করে।

---

## ব্যাকআপ এবং রোলব্যাক

### আপডেটের আগে

1. ডাটাবেস ব্যাকআপ নিন: cPanel → **Backup Wizard** বা phpMyAdmin → Export।
2. Theme এবং mu-plugin ফোল্ডার জিপ করে ডাউনলোড করুন।

### খারাপ ডিপ্লয়ের পর রোলব্যাক

**উপায় ১ — Git revert:**

```bash
git revert HEAD
git push origin main
```

**উপায় ২ — ব্যাকআপ জিপ থেকে রিস্টোর:**

1. ভাঙা ফোল্ডার ডিলিট বা রিনেম করুন।
2. ব্যাকআপ জিপ আপলোড করে Extract করুন।

**ডাটাবেস রোলব্যাক:**

1. phpMyAdmin-এ সব টেবিল ড্রপ করুন।
2. ব্যাকআপ `.sql` ইমপোর্ট করুন।

---

## সমস্যা সমাধান

### "Destination directory does not exist"

`deploy/cpanel-deploy.sh`-এ `DEST_DIR` আপনার সঠিক document root দিয়ে সেট করুন।

### Database connection error

1. `vsa-env-loader.php` `wp-content/mu-plugins/`-এ আছে কিনা দেখুন।
2. `.env` ফাইল `public_html`-এর বাইরে আছে কিনা দেখুন।
3. `wp-config.php`-এ `getenv()` ব্যবহার করা হয়েছে কিনা দেখুন।
4. cPanel-এ MySQL database credentials সঠিক কিনা যাচাই করুন।

### পরিবর্তন দেখা যাচ্ছে না

1. Cache plugin ক্লিয়ার করুন।
2. LiteSpeed cache ক্লিয়ার করুন।
3. Cloudflare cache ক্লিয়ার করুন।
4. সঠিক branch-এ push হয়েছে কিনা দেখুন।

### 500 Internal Server Error

1. `wp-content/debug.log` দেখুন।
2. cPanel error logs দেখুন।
3. PHP version 8.1 বা নতুন কিনা যাচাই করুন।
