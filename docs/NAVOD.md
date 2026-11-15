# Návod na inštaláciu LitebansU (aj pre začiatočníkov)

English version: [INSTALL.md](INSTALL.md).

Tento návod ide krok za krokom. Nič netreba vedieť programovať, stačí kopírovať príkazy. Návod má tri časti:

1. [Čo budeš potrebovať](#1-čo-budeš-potrebovať)
2. [Nahratie súborov a nastavenie](#2-nahratie-súborov-a-nastavenie)
3. [Kam web dať: vlastná doména, subdoména alebo priečinok](#3-kam-web-dať) (nginx aj Apache)
4. [Šablóny: ako prepnúť vzhľad a ako pridať vlastný](#4-šablóny)
5. [Keď niečo nejde](#5-keď-niečo-nejde)

> Konfigurácie nginx v tomto návode som pripravil podľa súborov projektu, ale na serveri som ich neskúšal. Vždy ich najprv skontroluj príkazom `sudo nginx -t`. Ten nič nerozbije, len povie, či je zápis v poriadku.

---

## 1. Čo budeš potrebovať

- Server s Linuxom (napr. Ubuntu) a prístupom cez SSH.
- **PHP 8.0 alebo novšie** s rozšíreniami `pdo_mysql`, `mbstring`, `intl`, `gd` (s podporou WebP), `curl`, `dom`, `openssl` a `zip`.
- **MySQL alebo MariaDB** s databázou, do ktorej píše plugin LiteBans.
- **nginx** alebo **Apache** s `mod_rewrite`.
- Doménu alebo subdoménu, ktorá smeruje na server (DNS záznam A na IP servera).

Rozšírenia PHP nainštaluješ takto (Ubuntu, verzia 8.3, ak máš inú, zmeň číslo):

```bash
sudo apt update
sudo apt install php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-intl php8.3-gd php8.3-curl php8.3-xml php8.3-zip unzip
```

Zistíš, či PHP vie WebP (potrebné pre hlavy hráčov a dôkazy):

```bash
php -r "var_dump(function_exists('imagewebp'));"
```

Ak vypíše `bool(true)`, je to v poriadku.

---

## 2. Nahratie súborov a nastavenie

### Krok 1: nahraj súbory

Rozbaľ projekt do priečinka, ktorý si vyberieš v kroku 3. Príklad pre samostatný web:

```bash
sudo mkdir -p /var/www/mcbans
sudo unzip LitebansU.zip -d /var/www/mcbans
```

### Krok 2: daj webovému serveru právo zapisovať

Do týchto troch priečinkov web ukladá dáta (používatelia, nahraté dôkazy, logy, hlavy hráčov):

```bash
cd /var/www/mcbans
sudo mkdir -p data/avatars demos/data logs site-templates
sudo chown -R www-data:www-data data demos/data logs site-templates
sudo chmod -R u+rwX data demos/data logs site-templates
```

(`www-data` je používateľ nginx aj Apache na Ubuntu. Inde to môže byť `nginx` alebo `apache`.)

### Krok 3: nastav web

Máš dve možnosti. Stačí jedna.

**A) Sprievodca.** Otvor v prehliadači `https://tvoja-domena.sk/install.php`, vyplň údaje (databáza, názov webu, jazyk, šablóna) a skopíruj hotový obsah do súboru `.env`.

**B) Ručne.** Skopíruj vzor a uprav ho:

```bash
cd /var/www/mcbans
cp .env_example_clean .env
nano .env
```

Najdôležitejšie riadky:

| Riadok | Čo znamená |
|---|---|
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | prístup do databázy LiteBans |
| `TABLE_PREFIX=litebans_` | predpona tabuliek (väčšinou nemeníš) |
| `SITE_URL=https://tvoja-domena.sk` | adresa webu |
| `ADMIN_PASSWORD=` | heslo admina (hash, viď nižšie) |
| `TEMPLATE=default` | vzhľad webu (`default` alebo `bulma`) |
| `AVATAR_CACHE=false` | `true` = hlavy hráčov sa stiahnu a uložia u teba |
| `ASSETS_URL=` | prázdne, ak nepoužívaš samostatnú doménu pre súbory |
| `TRUSTED_PROXIES=` | prázdne, ak nie si za Cloudflare alebo proxy |

**Heslo admina:** otvor `https://tvoja-domena.sk/hash.php`, zadaj heslo a výsledok (dlhý text začínajúci `$2y$`) vlož za `ADMIN_PASSWORD=`. Ak je v `.env` znak `$`, nechaj ho tak, ako ho súbor vypísal.

### Krok 4: zmaž inštalačné súbory

Po dokončení **vždy** zmaž:

```bash
rm /var/www/mcbans/install.php /var/www/mcbans/hash.php
```

Kým existujú, každý by mohol skúšať prepísať nastavenia.

---

## 3. Kam web dať

Vyber si jednu z troch možností.

| Možnosť | Adresa | Kedy |
|---|---|---|
| **A. Samostatný web** | `https://mcbans.sk` | máš vlastnú doménu len pre banlist |
| **B. Subdoména** | `https://bany.tvoja-domena.sk` | banlist pri hlavnom webe, vlastný vzhľad |
| **C. Priečinok** | `https://tvoja-domena.sk/mcbans` | nechceš zakladať ďalšiu doménu |

Aplikácia si sama zistí, či beží v priečinku, takže v `.env` nemusíš nič meniť. Dôležité je len správne nastaviť server.

### 3.1 nginx

Najprv zisti, ako sa volá PHP socket. Spusti:

```bash
ls /run/php/
```

Uvidíš napríklad `php8.3-fpm.sock`. Toto meno použi v konfigurácii nižšie na riadku `fastcgi_pass`.

#### A) a B) Samostatný web a subdoména

Obe možnosti sú rovnaké, líši sa len `server_name`. Vytvor súbor:

```bash
sudo nano /etc/nginx/sites-available/mcbans.conf
```

Vlož (zmeň `mcbans.sk`, cestu `root` a meno socketu):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name mcbans.sk;             # pri subdoméne napr. bany.tvoja-domena.sk

    root /var/www/mcbans;
    index index.php;
    charset utf-8;
    server_tokens off;
    client_max_body_size 32m;

    gzip on;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/json image/svg+xml;

    # ---- ZAKÁZANÉ: musí byť pred všetkým ostatným ----
    location ~ /\.(?!well-known/) { deny all; }
    location ~ ^/(config|core|controllers|templates|lang|data|logs|demos/data|nginx)(/|$) { deny all; }
    location ~ ^/(install|hash)\.php$ { deny all; }
    location ~* \.(md|log|sql|bak|backup|old|orig|swp|disabled|dist|lock|env|ini|sh)$ { deny all; }

    # ---- JEDINÉ PHP súbory, ktoré smú bežať ----
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

    # ---- Hlavy hráčov (generuje PHP, musí byť pred statickými súbormi, inak vrátia 404) ----
    location ^~ /avatar/ {
        rewrite ^ /index.php last;
    }

    # ---- Statické súbory (CSS, JS, obrázky, písma) ----
    location ~* \.(css|js|png|jpg|jpeg|gif|webp|avif|ico|woff|woff2|ttf)$ {
        expires 1y;
        access_log off;
        try_files $uri =404;
    }

    # ---- Všetko ostatné (stránky, hlavy hráčov, robots.txt, sitemap.xml, admin...) ----
    location / {
        rewrite ^ /index.php last;
    }
}
```

Zapni a skontroluj:

```bash
sudo ln -s /etc/nginx/sites-available/mcbans.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

**HTTPS (zámok v prehliadači)** získaš zadarmo:

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d mcbans.sk
```

Certbot upraví súbor sám a pridá presmerovanie na https.

> Ak už máš na serveri vlastný podrobný nginx súbor (napr. so Cloudflare, limitmi požiadaviek a logmi AI botov), pridaj do neho blok `location ^~ /avatar/ { rewrite ^ /index.php last; }` pred blok so statickými súbormi. Bez neho hlavy hráčov (`.webp`) vrátia 404, ak zapneš ich sťahovanie.

#### C) V priečinku `/mcbans` na existujúcom webe

Predpokladáme, že hlavný web má `root /var/www/html;` a banlist je v `/var/www/html/mcbans`. Do **existujúceho** bloku `server { ... }` hlavného webu vlož tento blok (zmeň len meno socketu):

```nginx
    location ^~ /mcbans/ {
        client_max_body_size 32m;

        # ---- ZAKÁZANÉ ----
        location ~ /\.(?!well-known/) { deny all; }
        location ~ ^/mcbans/(config|core|controllers|templates|lang|data|logs|demos/data|nginx)(/|$) { deny all; }
        location ~ ^/mcbans/(install|hash)\.php$ { deny all; }
        location ~* ^/mcbans/.+\.(md|log|sql|bak|backup|old|orig|swp|disabled|dist|lock|env|ini|sh)$ { deny all; }

        # ---- JEDINÉ PHP súbory ----
        location ~ ^/mcbans/(index|assets/bundle|demos/case-evidence)\.php$ {
            client_max_body_size 512m;
            client_body_timeout 600s;
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/run/php/php8.3-fpm.sock;
            fastcgi_read_timeout 600s;
        }
        location ~ \.php$ { return 404; }

        # ---- Hlavy hráčov (generuje PHP, musí byť pred statickými súbormi) ----
        location ^~ /mcbans/avatar/ {
            rewrite ^ /mcbans/index.php last;
        }

        # ---- Statické súbory ----
        location ~* \.(css|js|png|jpg|jpeg|gif|webp|avif|ico|woff|woff2|ttf)$ {
            expires 1y;
            try_files $uri =404;
        }

        # ---- Všetko ostatné ----
        rewrite ^ /mcbans/index.php last;
    }
```

Skontroluj a načítaj:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

Výsledok: `https://tvoja-domena.sk/mcbans`. Odkazy v aplikácii aj sitemap si priečinok `/mcbans` doplnia sami.

### 3.2 Apache

Apache číta súbor `.htaccess`, ktorý je súčasťou projektu, takže stačí povoliť jeho čítanie.

Zapni moduly (raz):

```bash
sudo a2enmod rewrite headers expires deflate
```

**A) Samostatný web a B) subdoména:**

```bash
sudo nano /etc/apache2/sites-available/mcbans.conf
```

```apache
<VirtualHost *:80>
    ServerName mcbans.sk
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
sudo certbot --apache -d mcbans.sk
```

**C) Priečinok:** nič nenastavuješ. Nahraj súbory do `/var/www/html/mcbans` a over, že pre hlavný web je v `Directory` zapnuté `AllowOverride All`.

### 3.3 Skontroluj, že všetko funguje

Otvor:

- `https://tvoja-domena.sk/` (alebo s `/mcbans`) vidíš úvodnú stránku,
- `/bans` zobrazí zoznam banov,
- `/robots.txt` zobrazí text,
- `/config/app.php` musí vrátiť **403 alebo 404** (nikdy nie obsah),
- `/data/` musí vrátiť **403 alebo 404**.

Ak sa niektorá z posledných dvoch ukáže, ihneď oprav konfiguráciu, inak sú vystavené tvoje heslá.

---

## 4. Šablóny

Šablóna je vzhľad celého webu. Dodávajú sa tri:

- **default**: pôvodný vzhľad (Bootstrap).
- **bulma**: nový vzhľad (Bulma), s tmavým a svetlým režimom.
- **frost**: pokojný, chladný, typografický vzhľad v ľadovo modrej a bridlicovej, bez frameworku, s tmavým a svetlým režimom.

### Prepnutie šablóny

1. Prihlás sa do administrácie.
2. Záložka **Settings** (Nastavenia), pole **Site Template**, vyber šablónu a stlač **Save Settings**.
3. Alebo záložka **SEO & Tracking**, sekcia **Templates**, tlačidlo **Activate**.
4. Alebo v súbore `.env` riadok `TEMPLATE=bulma`. Výber v admine má vždy prednosť pred `.env`.

### Čo šablóna mení a čo nie

Šablóna prepíše stránku len vtedy, keď pre ňu obsahuje súbor. Ak súbor chýba, tá stránka sa zobrazí **celá v predvolenom dizajne** (aj s hlavičkou a pätičkou). Preto sa vlastná šablóna nemusí robiť celá naraz.

Súbory, ktoré môže šablóna dodať (všetky sú nepovinné okrem prvých dvoch):

| Súbor v priečinku šablóny | Stránka |
|---|---|
| `header.php`, `footer.php` | hlavička a pätička (povinné) |
| `home.php` | úvodná stránka |
| `punishments.php` | zoznamy banov, mutes, varovaní a vyhodení |
| `detail.php` | detail trestu |
| `stats.php` | štatistiky |
| `protest.php` | žiadosť o unban |
| `search.php` | vyhľadávanie |
| `privacy.php` | ochrana osobných údajov |
| `error.php` | chybová stránka |
| `admin/login.php` | prihlásenie do administrácie |

Dve veci sú **vždy v predvolenom dizajne**, nech je aktívna akákoľvek šablóna: administračný panel (po prihlásení) a samostatné chybové obrazovky, ktoré sa zobrazia pred načítaním aplikácie (napr. pri chybe databázy).

Šablóna Bulma má všetky súbory v tabuľke, takže po jej zapnutí sa zmení celý verejný web aj prihlasovacia stránka.

### Pridanie vlastnej šablóny

Priečinok šablóny vyzerá takto:

```
moja-sablona/
  template.json
  header.php
  footer.php
  home.php            (nepovinné, ďalšie stránky podľa tabuľky)
  assets/
    style.css
```

Súbor `template.json`:

```json
{
  "id": "moja-sablona",
  "name": "Moja šablóna",
  "version": "1.0",
  "author": "Ja",
  "framework": "vlastný",
  "description": "Krátky popis"
}
```

`id` má mať 2 až 31 malých písmen, číslic alebo pomlčiek. Názvy `default`, `bulma` a `frost` sú vyhradené.

**Pridanie, možnosť 1 (admin):** zabaľ priečinok do `.zip` tak, aby `template.json` bol priamo v koreni zipu alebo v jednom priečinku. Vo vyššie uvedenom umiestnení **SEO & Tracking → Templates** vyber zip a stlač **Upload template**. Po nahratí sa objaví v zozname a v poli Site Template.

**Pridanie, možnosť 2 (ručne):** skopíruj priečinok do `/var/www/mcbans/site-templates/` a obnov admin. Nájde sa sám.

**Pravidlá nahrávania:** zip max 8 MB, povolené sú súbory `php, json, css, js, png, jpg, gif, webp, ico, woff, woff2, ttf, txt`. Súbory `svg` sa nepovoľujú (môžu obsahovať skript).

> **Bezpečnosť:** šablóna obsahuje PHP kód, ktorý beží na serveri s plnými právami webu. Nahrávaj len šablóny, ktorým dôveruješ. Nahrávať môže iba rola administrátor.

### Čo sa dá v šablóne použiť

V súboroch šablóny sú dostupné tieto premenné a funkcie:

- `$config` (nastavenia), `$lang->get('kluc')` (preklady), `$controller` (pomocné funkcie ako `formatDate`, `avatarUrl`), `$title`, `$currentPage`.
- `url('bans')` vytvorí adresu stránky, `asset('assets/css/icons.css')` adresu súboru s verziou.
- `\core\TemplateManager::assetUrl('moja-sablona', 'style.css')` vráti adresu súboru z priečinka `assets/` tvojej šablóny.
- Súbor `templates/partials/seo-head.php` obsahuje všetky SEO meta značky. Vlož ho do `header.php` takto: `<?php include __DIR__ . '/../../templates/partials/seo-head.php'; ?>`. Potom pridaj svoje CSS, `</head>` a `<body>`.
- Ikony sú SVG: `<i class="fas fa-ban"></i>`. Zoznam je v `assets/css/icons.css`.

Najľahšie je skopírovať priečinok `site-templates/bulma` a upravovať ho.

---

## 5. Keď niečo nejde

| Príznak | Čo skúsiť |
|---|---|
| Biela stránka alebo chyba 500 | `tail -n 30 /var/log/nginx/error.log` (alebo `/var/log/apache2/error.log`) a `logs/` v projekte |
| Nejdu štýly alebo obrázky | stlač Ctrl+F5, over cestu `root` v konfigurácii |
| Hlavy hráčov sú rozbité | v nginx pridaj `location ^~ /avatar/ { rewrite ^ /index.php last; }`, pozri sekciu 2, riadok `AVATAR_CACHE` |
| Nejde nahrať dôkaz alebo video | zvýš `upload_max_filesize` a `post_max_size` na `512M` v `php.ini` (napr. `/etc/php/8.3/fpm/php.ini`), potom `sudo systemctl restart php8.3-fpm` |
| Nejde zápis (uloženie nastavení) | skontroluj práva: `sudo chown -R www-data:www-data data demos/data logs site-templates` |
| Šablónu nemožno nahrať | chýba PHP rozšírenie `zip` (`sudo apt install php8.3-zip`) alebo je zip väčší ako 8 MB |
| Za Cloudflare vidím všetkých z jednej IP | do `.env` daj `TRUSTED_PROXIES=` s IP adresami Cloudflare |
| Po zmene šablóny sa nič nezmenilo | vymaž cache prehliadača (Ctrl+F5); ak máš opcache, `sudo systemctl reload php8.3-fpm` |

Ak si nevieš rady, pošli výpis z `error.log` (bez hesiel) a popis, čo si robil.
