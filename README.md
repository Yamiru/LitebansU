# LiteBans Modern Web Interface

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/Yamiru/LitebansU.svg)](https://github.com/Yamiru/LitebansU/releases/)
[![GitHub stars](https://img.shields.io/github/stars/Yamiru/LitebansU.svg)](https://github.com/Yamiru/LitebansU/stargazers)

**A modern, secure, and responsive web interface for LiteBans punishment management system.**

---

## 🌐 Live Demo (optional)

[https://mc.proserver.sk](https://mc.proserver.sk)


## Screenshot
![Imgur Image](https://i.imgur.com/YJXukd9.png)


## ✨ Features

- **🎨 Modern UI/UX** - Clean, responsive design with smooth animations and dark/light themes
- **🌍 Multi-language Support** - English, Slovak, Russian, German, Spanish, French
- **🔍 Real-time Search** - Instant player punishment search with debouncing
- **🛡️ Security First** - CSRF protection, XSS prevention, SQL injection protection
- **📱 Mobile Responsive** - Works perfectly on all devices and screen sizes
- **⚡ Performance Optimized** - Lazy loading, caching, and minimal resource usage
- **🔧 Easy Installation** - Simple download and copy setup
- **🎯 SEO Optimized** - Full SEO meta tags and Open Graph support

## 🚀 Quick Start

### Download and Install

1. **Download the latest release**
   ```bash
   wget https://github.com/Yamiru/LitebansU/archive/refs/tags/LitebansU.zip
   # or download from GitHub releases page
   ```

2. **Extract to your web directory**
   ```bash
   unzip LitebansU.zip
   cp -r LitebansU/* /var/www/html/litebans/
   ```

3. **Set permissions**
   ```bash
   chmod 755 /var/www/html/litebans
   chmod 644 /var/www/html/litebans/.htaccess
   ```

3. **create** .htaccess https://github.com/Yamiru/LitebansU/blob/main/.htaccess
   ```
   nano .htaccess
   ```


## 📋 Requirements

### Server Requirements
- **PHP 8.0+** with extensions:
  - PDO & pdo_mysql
  - mbstring
  - intl
  - session
  - json
- **MySQL 5.7+** or **MariaDB 10.3+**
- **Web Server**: Apache 2.4+ or Nginx 1.18+

### LiteBans Plugin
- **LiteBans 2.8.0+** installed on your Minecraft server
- Database access to LiteBans tables

## ⚙️ Configuration

### 1. Database Settings

Edit `database.php` file with your database credentials:

```database.php
        // Your Database
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->port = (int)($_ENV['DB_PORT'] ?? 3306);
        $this->database = $_ENV['DB_NAME'] ?? '';
        $this->username = $_ENV['DB_USER'] ?? '';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->driver = $_ENV['DB_DRIVER'] ?? 'mysql';

```

### 2. Site Configuration and SEO
Edit `index.php` file 
```index.php
// Configuration
$config = [
    'site_name' => $_ENV['SITE_NAME'] ?? 'LiteBans',
    'items_per_page' => (int)($_ENV['ITEMS_PER_PAGE'] ?? 20),
    'timezone' => $_ENV['TIMEZONE'] ?? 'UTC',
    'date_format' => $_ENV['DATE_FORMAT'] ?? 'Y-m-d H:i:s',
    'avatar_url' => $_ENV['AVATAR_URL'] ?? 'https://crafatar.com/avatars/{uuid}?size=32&overlay=true',
    'avatar_url_offline' => $_ENV['AVATAR_URL_OFFLINE'] ?? 'https://minotar.net/avatar/{name}/32',
    'base_path' => BASE_PATH,
    'debug' => ($_ENV['DEBUG'] ?? 'false') === 'true',
    
    // SEO Configuration
    'site_url' => $_ENV['SITE_URL'] ?? 'http://localhost',
    'site_lang' => $_ENV['SITE_LANG'] ?? 'en',
    'site_charset' => $_ENV['SITE_CHARSET'] ?? 'UTF-8',
    'site_viewport' => $_ENV['SITE_VIEWPORT'] ?? 'width=device-width, initial-scale=1.0',
    'site_robots' => $_ENV['SITE_ROBOTS'] ?? 'index, follow',
    'site_description' => $_ENV['SITE_DESCRIPTION'] ?? 'Public interface for viewing server punishments and bans',
    'site_title_template' => $_ENV['SITE_TITLE_TEMPLATE'] ?? '{page} - {site}',
    'site_favicon' => $_ENV['SITE_FAVICON'] ?? '/favicon.ico',
    'site_apple_icon' => $_ENV['SITE_APPLE_ICON'] ?? '/apple-touch-icon.png',
    'site_theme_color' => $_ENV['SITE_THEME_COLOR'] ?? '#6366f1',
    'site_og_image' => $_ENV['SITE_OG_IMAGE'] ?? null,
    'site_twitter_site' => $_ENV['SITE_TWITTER_SITE'] ?? null,
    'site_keywords' => $_ENV['SITE_KEYWORDS'] ?? null,
    'site_author' => $_ENV['SITE_AUTHOR'] ?? null,
    'site_generator' => $_ENV['SITE_GENERATOR'] ?? 'LitebansU'

```


## 🎯 Usage

### Navigation
- **Home** - Server statistics and recent activity
- **Bans** - View all bans with pagination
- **Mutes** - View all mutes with pagination  
- **Warnings** - View all warnings
- **Kicks** - View all kicks

### Search
- Search by player name or UUID
- Real-time search with auto-suggestions
- View complete punishment history

### Themes
- **Light Theme** - Clean white interface
- **Dark Theme** - Eye-friendly dark interface
- **Auto Theme** - Follows system preference

### Languages
Switch between supported languages:
- 🇺🇸 English
- 🇸🇰 Slovenčina  
- 🇷🇺 Русский
- 🇩🇪 Deutsch
- 🇪🇸 Español
- 🇫🇷 Français

## 🔧 Advanced Configuration

### Web Server Setup

#### Apache (.htaccess included)
The included `.htaccess` file handles URL rewriting automatically.

### Adding Custom Languages

1. Create new language file: `lang/xx.php`
2. Copy structure from `lang/en.php`
3. Translate all strings
4. Add language code to `LanguageManager.php`:
   ```php
   private const SUPPORTED_LANGS = ['en', 'sk', 'ru', 'de', 'es', 'fr', 'xx'];
   ```
5. add to index.php 

### Custom Themes

Edit CSS variables in `assets/css/main.css`:
```css
.theme-custom {
    --primary: #your-color;
    --bg-primary: #your-background;
    /* ... other variables */
}
```

## 🐛 Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
- add database

#### 2. Theme/Language Switcher Not Working
- Clear browser cache (Ctrl+F5)
- Check JavaScript console for errors
- Verify cookies are enabled
- Ensure `.htaccess` is working (Apache)

#### 3. Search Not Working
- Check CSRF token generation
- Verify JavaScript is enabled
- Check rate limiting settings
- Ensure database permissions


### File Permissions Check
```bash
# Set correct permissions
find /var/www/html/litebans -type f -exec chmod 644 {} \;
find /var/www/html/litebans -type d -exec chmod 755 {} \;
```

## 📁 Directory Structure

```
litebans/
├── assets/
│   ├── css/
│   │   └── main.css          # Main stylesheet
│   └── js/
│       └── main.js           # JavaScript functionality
├── config/
│   └── database.php          # Database configuration
├── controllers/
│   ├── HomeController.php    # Home page logic
│   └── PunishmentsController.php # Punishments logic
├── core/
│   ├── BaseController.php    # Base controller class
│   ├── DatabaseRepository.php # Database operations
│   ├── LanguageManager.php   # Language handling
│   ├── SecurityManager.php   # Security functions
│   └── ThemeManager.php      # Theme management
├── lang/
│   ├── en.php               # English translations
│   ├── sk.php               # Slovak translations
│   ├── ru.php               # Russian translations
│   └── ...                  # Other languages
├── templates/
│   ├── header.php           # Page header
│   ├── footer.php           # Page footer
│   ├── home.php             # Home page template
│   └── punishments.php      # Punishments template
├── .htaccess                # Apache configuration
├── index.php                # Main entry point
└── README.md                # This file
```

## 🛡️ Security Features

- **CSRF Protection** - All forms include CSRF tokens
- **XSS Prevention** - All output is properly escaped
- **SQL Injection Protection** - PDO prepared statements
- **Rate Limiting** - Prevents brute force attacks
- **Secure Sessions** - HTTPOnly, Secure, SameSite cookies
- **Security Headers** - X-Frame-Options, CSP, etc.
- **Input Validation** - Strict input filtering and sanitization

## 🎨 Customization

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## 📊 Performance Tips

### Production Optimizations
1. **Enable OPcache** in PHP
2. **Use PHP-FPM** instead of mod_php
3. **Enable Gzip compression** (included in .htaccess)
4. **Set up CloudFlare** for CDN and caching
5. **Optimize MySQL** queries and indexes


## 🌟 Roadmap

- [ ] .env - Simple edit configuration (currently not working due to a loading error)


## 📞 Support

- **GitHub Issues**: [Report bugs or request features](https://github.com/Yamiru/LitebansU/issues)
- **Discord**: [Discord](https://discord.gg/jNVwwcQ)

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Credits

- **Original LiteBans Plugin**: [Ruben](https://www.spigotmc.org/resources/3715/)
- **Author**: [Yamiru](https://github.com/Yamiru)
- **Icons**: [Font Awesome](https://fontawesome.com/)
- **Fonts**: [Inter](https://rsms.me/inter/) by Rasmus Andersson

## ⭐ Show Your Support

If this project helped you, please consider:
- ⭐ **Starring** the repository
- 🐛 **Reporting bugs** or suggesting features
- 🤝 **Contributing** to the codebase
- 💬 **Sharing** with your community

---

<div align="center">

**Made with ❤️ for the Minecraft community**

[Website](https://github.com/Yamiru/LitebansU) • [Documentation](https://github.com/Yamiru/LitebansU/wiki) • [Issues](https://github.com/Yamiru/LitebansU/issues) • [Discord](https://discord.gg/jNVwwcQ)

</div>
