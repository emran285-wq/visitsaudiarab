# VisitSaudiArab.com — WordPress Theme + Core Plugin

This repository contains the custom WordPress theme and must-use plugin for
**VisitSaudiArab.com**. It is designed to be deployed with **cPanel Git Version
Control** while keeping secrets and uploads out of Git.

## What's in this repository

```
mu-plugins/visitsaudiarab-core.php   → custom post types, taxonomies, meta boxes,
                                         JSON-LD schema, search noindex
mu-plugins/vsa-env-loader.php        → loads database credentials and salts from
                                         a file outside public_html
theme/                                → custom WordPress theme
  style.css                          → theme header + all CSS
  functions.php                      → setup, hreflang, breadcrumbs, perf, favicon
  header.php / footer.php
  assets/images/                     → favicon, apple-touch-icon, logo source
  front-page.php                     → homepage (hero, trending, plan-trip)
  single-destination.php             → destination hub template
  single-attraction.php              → attraction page w/ schema-ready fields
  single-sa_event.php                → event page (past-event handling)
  single.php / page.php / archive.php / index.php / search.php / 404.php
  template-parts/know-before-you-go.php
robots.txt                            → crawler directives (update domain before deploy)
htaccess-additions.txt                → security/caching rules to merge into .htaccess
migrations/                           → reserved for future, non-destructive migrations
deploy/cpanel-deploy.sh               → safe deployment script used by .cpanel.yml
.cpanel.yml                           → cPanel Git Version Control deployment manifest
.env.example                          → placeholder environment configuration
.gitignore                            → excludes secrets, uploads, logs, backups
```

## Stack

- **Platform:** WordPress (self-hosted)
- **PHP:** 8.1 or newer
- **Database:** MySQL 5.7+ / MariaDB 10.3+ (matches WordPress requirements)
- **Web server:** Apache with `mod_rewrite` enabled
- **Theme:** Custom lightweight theme, no page-builder dependency
- **Multilingual:** Lightweight `vsa_lang` / `vsa_translation_id` custom-field
  approach (no WPML/Polylang required; adopt one later if desired)
- **No custom tables:** schema is WordPress core + the post types/taxonomies
  declared in the must-use plugin
- **No Node.js build step:** CSS is plain CSS; no frontend bundler is required
  to run the production site

## Environment configuration

1. Copy `.env.example` to a safe location **outside** `public_html`, e.g.
   `/home/yourcpaneluser/private/visitsaudiarab.env`.
2. Replace every placeholder with your real cPanel values.
3. Add this line at the **very top** of `wp-config.php`, before any `define()` calls:

   ```php
   require_once __DIR__ . '/wp-content/mu-plugins/vsa-env-loader.php';
   ```

4. Update the `define()` calls in `wp-config.php` to read from the environment:

   ```php
   define( 'DB_NAME',     getenv( 'DB_NAME' )     ?: 'your_database_name' );
   define( 'DB_USER',     getenv( 'DB_USER' )     ?: 'your_database_user' );
   define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) ?: 'your_database_password' );
   define( 'DB_HOST',     getenv( 'DB_HOST' )     ?: 'localhost' );
   define( 'DB_CHARSET',  getenv( 'DB_CHARSET' )  ?: 'utf8mb4' );
   define( 'DB_COLLATE',  getenv( 'DB_COLLATE' )  ?: '' );

   define( 'WP_HOME',    getenv( 'WP_HOME' )    ?: 'https://visitsaudiarab.com' );
   define( 'WP_SITEURL', getenv( 'WP_SITEURL' ) ?: 'https://visitsaudiarab.com' );

   define( 'WP_DEBUG',         filter_var( getenv( 'WP_DEBUG' ) ?: false, FILTER_VALIDATE_BOOL ) );
   define( 'WP_DEBUG_LOG',     filter_var( getenv( 'WP_DEBUG_LOG' ) ?: false, FILTER_VALIDATE_BOOL ) );
   define( 'WP_DEBUG_DISPLAY', false );
   ```

5. Keep the WordPress salt lines in `wp-config.php` or move them into the `.env`
   file so they are also outside version control.

See `DEPLOYMENT_CPANEL.md` for the full step-by-step deployment guide.

## Local development

1. Install WordPress locally (XAMPP, LocalWP, Docker, etc.).
2. Copy `theme/` to `wp-content/themes/visitsaudiarab/`.
3. Copy `mu-plugins/*.php` to `wp-content/mu-plugins/`.
4. Activate **VisitSaudiArab** in wp-admin → Appearance → Themes.
5. Go to Settings → Permalinks and click **Save** to flush rewrite rules.
6. For local environment values, create a `.env` file outside the document root
   or place one in the repository root (it is gitignored). Never commit it.

## Security notes

- `wp-config.php` and `.env` are **not** tracked in this repository.
- The real environment file should live outside `public_html`.
- `htaccess-additions.txt` disables directory listing, forces HTTPS, and adds
  security headers. Merge it into the live `.htaccess` on first deployment.
- Robots.txt contains a hardcoded sitemap URL; update it if your canonical
  domain changes.

## Required cPanel/hosting values

You must supply the following before the first deployment:

- cPanel username and document root path
- MySQL database name, user, and password
- Production domain (for `WP_HOME` / `WP_SITEURL`)
- WordPress salts (generate at https://api.wordpress.org/secret-key/1.1/salt/)
- SSL certificate (enable HTTPS)

## Updating the live site

After the initial setup, simply push commits to the connected GitHub branch.
cPanel Git Version Control will pull the changes and run `deploy/cpanel-deploy.sh`
to copy the updated theme and plugin files into `public_html`. Uploads, logs,
`wp-config.php`, and the `.env` file are never modified by the deploy script.

## Backups and rollback

- Always back up the database before applying updates.
- Keep a dated zip of `wp-content/themes/visitsaudiarab/` and
  `wp-content/mu-plugins/` before major changes.
- To roll back, restore the previous theme/plugin files via cPanel File Manager
  or by reverting the Git commit and re-deploying.

## Post-launch checklist

1. Create the WordPress menus (Appearance → Menus) and assign them to
   **Primary Menu**, **Mobile Menu**, and **Footer Menu**.
2. Create required pages: `/about/`, `/contact/`, `/editorial-policy/`,
   `/how-we-review/`, `/authors/`, `/advertise/`, `/privacy/`, `/cookies/`,
   `/affiliate-disclosure/`.
3. Install recommended plugins: SEO plugin, LiteSpeed Cache, contact form, 2FA.
4. Configure Cloudflare (or equivalent) for SSL, caching, and security.
5. Submit the sitemap to Google Search Console and Bing Webmaster Tools.
6. Validate JSON-LD output with Google's Rich Results Test.
7. Confirm `/search/` pages carry `noindex,follow`.
8. Run Lighthouse and Core Web Vitals checks.
