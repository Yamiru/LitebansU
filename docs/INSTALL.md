# LitebansU installation guide (beginner friendly)

This guide goes step by step. You do not need to write any code, just copy the commands. Slovak version: [NAVOD.md](NAVOD.md).

1. [What you need](#1-what-you-need)
2. [Uploading the files and configuring](#2-uploading-the-files-and-configuring)
3. [Where to put the site: own domain, subdomain or folder](#3-where-to-put-the-site) (nginx and Apache)
4. [Templates: switching the look and adding your own](#4-templates)
5. [When something does not work](#5-when-something-does-not-work)

> The nginx configs in this guide follow the project files but were not tested on a live server. Always check them with `sudo nginx -t` first. It changes nothing, it only tells you whether the syntax is valid.

---

## 1. What you need

- A Linux server (for example Ubuntu) with SSH access.
- **PHP 8.0 or newer** with the extensions `pdo_mysql`, `mbstring`, `intl`, `gd` (with WebP support), `curl`, `dom`, `openssl` and `zip`.
- **MySQL or MariaDB** with the database the LiteBans plugin writes to.
- **nginx** or **Apache** with `mod_rewrite`.
- A domain or subdomain pointing to the server (DNS A record to the server IP).

Install the PHP extensions like this (Ubuntu, version 8.3; change the number if you have another one):

```bash
sudo apt update
sudo apt install php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-intl php8.3-gd php8.3-curl php8.3-xml php8.3-zip unzip
```

Check that PHP can write WebP (needed for player heads and evidence):

```bash
php -r "var_dump(function_exists('imagewebp'));"
```

`bool(true)` means it is fine.

---

## 2. Uploading the files and configuring

### Step 1: upload the files

Extract the project into the folder you choose in step 3. Example for a separate site:

```bash
sudo mkdir -p /var/www/mcbans
sudo unzip LitebansU.zip -d /var/www/mcbans
```

### Step 2: let the web server write

The site stores data in these folders (users, uploaded evidence, logs, player heads, uploaded templates):

```bash
cd /var/www/mcbans
sudo mkdir -p data/avatars demos/data logs site-templates
sudo chown -R www-data:www-data data demos/data logs site-templates
sudo chmod -R u+rwX data demos/data logs site-templates
```

(`www-data` is the nginx and Apache user on Ubuntu. Elsewhere it may be `nginx` or `apache`.)

### Step 3: configure the site

Two options. Pick one.

**A) The installer.** Open `https://your-domain.com/install.php`, fill in the details (database, site name, language, template) and copy the generated content into a file named `.env`.

**B) By hand.** Copy the example and edit it:

```bash
cd /var/www/mcbans
cp .env_example_clean .env
nano .env
```

The most important lines:

| Line | Meaning |
|---|---|
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | access to the LiteBans database |
| `TABLE_PREFIX=litebans_` | table prefix (usually unchanged) |
| `SITE_URL=https://your-domain.com` | address of the site |
| `ADMIN_PASSWORD=` | admin password (a hash, see below) |
| `TEMPLATE=default` | look of the site (`default`, `bulma` or `frost`) |
| `AVATAR_CACHE=false` | `true` = player heads are downloaded and stored on your server |
| `ASSETS_URL=` | empty unless you use a separate cookie-free domain for static files |
| `TRUSTED_PROXIES=` | empty unless you are behind Cloudflare or another proxy |

**Admin password:** open `https://your-domain.com/hash.php`, enter a password and paste the result (a long text starting with `$2y$`) after `ADMIN_PASSWORD=`. Keep any `$` characters exactly as printed.

### Step 4: delete the installer files

When you are done, **always** delete:

```bash
rm /var/www/mcbans/install.php /var/www/mcbans/hash.php
```

While they exist, anyone could try to overwrite your settings.

---

## 3. Where to put the site

Choose one of three options.

| Option | Address | When |
|---|---|---|
| **A. Separate site** | `https://mcbans.com` | you have a domain only for the banlist |
| **B. Subdomain** | `https://bans.your-domain.com` | banlist next to your main site, own look |
| **C. Folder** | `https://your-domain.com/mcbans` | you do not want another domain |

The application detects by itself whether it runs in a folder, so you change nothing in `.env`. You only need to set up the server correctly.

### 3.1 nginx

First find the name of the PHP socket:

```bash
ls /run/php/
```

You will see something like `php8.3-fpm.sock`. Use that name on the `fastcgi_pass` lines below.

#### A) and B) Separate site and subdomain

Both are the same, only `server_name` differs. Create the file:

```bash
sudo nano /etc/nginx/sites-available/mcbans.conf
```

Paste this (change `mcbans.com`, the `root` path and the socket name):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name mcbans.com;             # for a subdomain e.g. bans.your-domain.com

    root /var/www/mcbans;
    index index.php;
    charset utf-8;
    server_tokens off;
    client_max_body_size 32m;

    gzip on;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/json image/svg+xml;

    # ---- BLOCKED: must come before everything else ----
    location ~ /\.(?!well-known/) { deny all; }
    location ~ ^/(config|core|controllers|templates|lang|data|logs|demos/data|nginx)(/|$) { deny all; }
    location ~ ^/(install|hash)\.php$ { deny all; }
    location ~* \.(md|log|sql|bak|backup|old|orig|swp|disabled|dist|lock|env|ini|sh)$ { deny all; }

    # ---- The ONLY PHP files allowed to run ----
    location = /index.php {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
    location = /assets/bundle.php {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
    location = /demos/case-evidence.php {
        client_max_body_size 512m;
        client_body_timeout 600s;
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_read_timeout 600s;
        fastcgi_max_temp_file_size 0;
    }
    location ~ \.php$ { return 404; }

    # ---- Player heads (generated by PHP; must come before static files or they return 404) ----
    location ^~ /avatar/ {
        rewrite ^ /index.php last;
    }

    # ---- Static files (CSS, JS, images, fonts) ----
    location ~* \.(css|js|png|jpg|jpeg|gif|webp|avif|ico|woff|woff2|ttf)$ {
        expires 1y;
        access_log off;
        try_files $uri =404;
    }

    # ---- Everything else (pages, robots.txt, sitemap.xml, admin...) ----
    location / {
        rewrite ^ /index.php last;
    }
}
```

Enable it and check:

```bash
sudo ln -s /etc/nginx/sites-available/mcbans.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

**HTTPS (the padlock in the browser)** is free:

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d mcbans.com
```

Certbot edits the file itself and adds the redirect to https.

> If you already have your own detailed nginx file (for example with Cloudflare, rate limits and AI bot logs), just add the block `location ^~ /avatar/ { rewrite ^ /index.php last; }` before the static files block. Without it, player heads (`.webp`) return 404 once you turn on head downloading.

#### C) In a `/mcbans` folder of an existing site

This assumes the main site has `root /var/www/html;` and the banlist is in `/var/www/html/mcbans`. Paste this block inside the **existing** `server { ... }` block of the main site (change only the socket name):

```nginx
    location ^~ /mcbans/ {
        client_max_body_size 32m;

        # ---- BLOCKED ----
        location ~ /\.(?!well-known/) { deny all; }
        location ~ ^/mcbans/(config|core|controllers|templates|lang|data|logs|demos/data|nginx)(/|$) { deny all; }
        location ~ ^/mcbans/(install|hash)\.php$ { deny all; }
        location ~* ^/mcbans/.+\.(md|log|sql|bak|backup|old|orig|swp|disabled|dist|lock|env|ini|sh)$ { deny all; }

        # ---- The ONLY PHP files allowed to run ----
        location ~ ^/mcbans/(index|assets/bundle|demos/case-evidence)\.php$ {
            client_max_body_size 512m;
            client_body_timeout 600s;
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/run/php/php8.3-fpm.sock;
            fastcgi_read_timeout 600s;
        }
        location ~ \.php$ { return 404; }

        # ---- Player heads (generated by PHP; must come before static files) ----
        location ^~ /mcbans/avatar/ {
            rewrite ^ /mcbans/index.php last;
        }

        # ---- Static files ----
        location ~* \.(css|js|png|jpg|jpeg|gif|webp|avif|ico|woff|woff2|ttf)$ {
            expires 1y;
            try_files $uri =404;
        }

        # ---- Everything else ----
        rewrite ^ /mcbans/index.php last;
    }
```

Check and reload:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

Result: `https://your-domain.com/mcbans`. Links and the sitemap add the `/mcbans` folder by themselves.

### 3.2 Apache

Apache reads the `.htaccess` that ships with the project, so you only have to allow it.

Enable the modules (once):

```bash
sudo a2enmod rewrite headers expires deflate
```

**A) Separate site and B) subdomain:**

```bash
sudo nano /etc/apache2/sites-available/mcbans.conf
```

```apache
<VirtualHost *:80>
    ServerName mcbans.com
    DocumentRoot /var/www/mcbans

    <Directory /var/www/mcbans>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

```bash
sudo a2ensite mcbans
sudo apache2ctl configtest
sudo systemctl reload apache2
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d mcbans.com
```

**C) Folder:** nothing to configure. Upload the files to `/var/www/html/mcbans` and make sure `AllowOverride All` is on for the main site's `Directory`.

### 3.3 Check that it works

Open:

- `https://your-domain.com/` (or with `/mcbans`) shows the home page,
- `/bans` shows the list of bans,
- `/robots.txt` shows text,
- `/config/app.php` must return **403 or 404** (never the file content),
- `/data/` must return **403 or 404**.

If either of the last two shows anything, fix the configuration immediately, otherwise your passwords are exposed.

---

## 4. Templates

A template is the look of the whole site. Three come with it:

- **default**: the original look (Bootstrap).
- **bulma**: a newer look (Bulma) with dark and light mode.
- **frost**: a calm, cold, typographic look in ice blue and slate, no framework, with dark and light mode.

### Switching the template

1. Sign in to the admin.
2. **Settings**, field **Site Template**, pick one and press **Save Settings**.
3. Or **SEO & Tracking**, section **Templates**, button **Activate**.
4. Or set `TEMPLATE=bulma` in `.env`. The choice made in the admin always wins over `.env`.

### What a template changes and what it does not

A template restyles a page only if it contains a file for it. If the file is missing, that page is shown **entirely in the default design** (header and footer included). So a custom template does not have to be complete.

Files a template can provide (all optional except the first two):

| File in the template folder | Page |
|---|---|
| `header.php`, `footer.php` | header and footer (required) |
| `home.php` | home page |
| `punishments.php` | lists of bans, mutes, warnings and kicks |
| `detail.php` | punishment detail |
| `stats.php` | statistics |
| `protest.php` | unban request page |
| `search.php` | search |
| `privacy.php` | privacy page |
| `error.php` | error page |
| `admin/login.php` | admin sign-in page |

Two things are **always in the default design**, whichever template is active: the admin panel after sign-in, and the standalone error screens shown before the application starts (for example a database error).

Bulma and Frost provide every file in the table, so switching to either one changes the whole public site and the sign-in page.

### Adding your own template

A template folder looks like this:

```
my-template/
  template.json
  header.php
  footer.php
  home.php            (optional, more pages per the table)
  assets/
    style.css
```

`template.json`:

```json
{
  "id": "my-template",
  "name": "My template",
  "version": "1.0",
  "author": "Me",
  "framework": "custom",
  "description": "Short description"
}
```

`id` must be 2 to 31 lowercase letters, digits or dashes. The names `default`, `bulma` and `frost` are reserved.

**Adding it, option 1 (admin):** zip the folder so that `template.json` is directly in the root of the zip or in a single folder. Under **SEO & Tracking → Templates** choose the zip and press **Upload template**. After upload it appears in the list and in the Site Template field.

**Adding it, option 2 (by hand):** copy the folder into `/var/www/mcbans/site-templates/` and reload the admin. It is found automatically.

**Upload rules:** zip up to 8 MB; allowed files are `php, json, css, js, png, jpg, gif, webp, ico, woff, woff2, ttf, txt`. `svg` files are not allowed (they can carry scripts).

> **Security:** a template contains PHP code that runs on your server with the full rights of the site. Upload only templates you trust. Only the administrator role can upload.

### What you can use inside a template

These variables and functions are available in template files:

- `$config` (settings), `$lang->get('key')` (translations), `$controller` (helpers such as `formatDate`, `avatarUrl`), `$title`, `$currentPage`.
- `url('bans')` builds a page address, `asset('assets/css/icons.css')` builds a file address with a version.
- `\core\TemplateManager::assetUrl('my-template', 'style.css')` returns the address of a file in your template's `assets/` folder.
- `templates/partials/seo-head.php` holds all SEO meta tags. Include it in `header.php` like this: `<?php include __DIR__ . '/../../templates/partials/seo-head.php'; ?>`. Then add your CSS, `</head>` and `<body>`.
- Icons are SVG: `<i class="fas fa-ban"></i>`. The list is in `assets/css/icons.css`.

The easiest start is to copy `site-templates/frost` (plain CSS) or `site-templates/bulma` and edit it.

---

## 5. When something does not work

| Symptom | What to try |
|---|---|
| Blank page or error 500 | `tail -n 30 /var/log/nginx/error.log` (or `/var/log/apache2/error.log`) and `logs/` in the project |
| Styles or images missing | press Ctrl+F5, check the `root` path in the configuration |
| Player heads are broken | in nginx add `location ^~ /avatar/ { rewrite ^ /index.php last; }`, see the `AVATAR_CACHE` line in section 2 |
| Evidence or video upload fails | raise `upload_max_filesize` and `post_max_size` to `512M` in `php.ini` (for example `/etc/php/8.3/fpm/php.ini`), then `sudo systemctl restart php8.3-fpm` |
| Saving settings fails | check permissions: `sudo chown -R www-data:www-data data demos/data logs site-templates` |
| A template cannot be uploaded | the PHP `zip` extension is missing (`sudo apt install php8.3-zip`) or the zip is larger than 8 MB |
| Behind Cloudflare everyone shows one IP | set `TRUSTED_PROXIES=` in `.env` to the Cloudflare IP addresses |
| Nothing changed after switching template | clear the browser cache (Ctrl+F5); with opcache, `sudo systemctl reload php8.3-fpm` |

If you are stuck, send the `error.log` output (without passwords) and a description of what you did.
