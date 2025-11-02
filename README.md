# LiteBans Modern Web Interface

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/Yamiru/LitebansU.svg)](https://github.com/Yamiru/LitebansU/releases/)
[![GitHub stars](https://img.shields.io/github/stars/Yamiru/LitebansU.svg)](https://github.com/Yamiru/LitebansU/stargazers)

**A modern, secure, and responsive web interface for LiteBans punishment management system.**

---

## 🌐 Live Demo (optional)

[https://yamiru.com/litebansU](https://yamiru.com/litebansU)


## Screenshot
![Imgur Image](https://i.imgur.com/YJXukd9.png)


## ✨ Features

- **🎨 Modern UI/UX** - Clean, responsive design with smooth animations and dark/light themes
- **🌍 Multi-language Support** - Arabic, Czech, German, Greek, English, Spanish, French, Hungarian, Italian, Japanese, Polish, Romanian, Russian, Slovak, Serbian, Turkish, Chinese (Simplified) 
- **📈 Statistics** View server punishment statistics
- **🔍 Real-time Search** - Instant player punishment search with debouncing
- **🛡️ Security First** - CSRF protection, XSS prevention, SQL injection protection
- **📱 Mobile Responsive** - Works perfectly on all devices and screen sizes
- **⚡ Performance Optimized** - Lazy loading, caching, and minimal resource usage
- **🔧 Easy Installation** - Simple download and copy setup
- **⚙️ Admin Panel** - Manage punishments, export/import data, and view system information.
- **🎯 SEO Optimized** - Full SEO meta tags and Open Graph support
- **▶️ Demo Management** - Advanced Evidence Management

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
  - gd/imagick
  - curl
- **MySQL 5.7+** or **MariaDB 10.3+**
- **Web Server**: Apache 2.4+ (mod_rewrite) or Nginx 1.18+

### LiteBans Plugin
- **LiteBans 2.8.0+** installed on your Minecraft server
- Database access to LiteBans tables

## ⚙️ Configuration

### 1. Database Settings

Edit `.env` file with your database credentials:

```.env
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=
DB_USER=
DB_PASS=
DB_DRIVER=mysql
TABLE_PREFIX=litebans_
```

### 2. Site Configuration and SEO
Edit `.env` file 
```.env
# Site Configuration
SITE_NAME=LiteBansU
FOOTER_SITE_NAME=YourSite
ITEMS_PER_PAGE=100
TIMEZONE=UTC
DATE_FORMAT=Y-m-d H:i:s
BASE_URL=https://YourSite.com

# SEO Configuration
SITE_URL=https://YourSite.com
SITE_CHARSET=UTF-8
SITE_VIEWPORT=width=device-width, initial-scale=1.0
SITE_ROBOTS=index, follow
SITE_DESCRIPTION=View and search player punishments on our Minecraft server
SITE_TITLE_TEMPLATE={page} | {site}
SITE_THEME_COLOR=#ef4444
SITE_OG_IMAGE=https://YourSite.com/og-image.png
SITE_TWITTER_SITE=@yourtwitter
SITE_KEYWORDS=minecraft,litebans,punishments,bans,mutes,server

# Default Settings
DEFAULT_THEME=dark
DEFAULT_LANGUAGE=en
SHOW_PLAYER_UUID=false
# Protest Configuration
PROTEST_DISCORD=https://discord.gg/
PROTEST_EMAIL=info@YourSite.com
PROTEST_FORUM=https://forum.YourSite.com/ban-protests

```
### 3. Admin Install

Edit `.env` and enable :

open url https://yourSite.com/hash.php add password copy to inside .env file and remove hash.php

## 🎯 Usage

### Navigation
- **Home** - Server statistics and recent activity
- **Bans** - View all bans with pagination
- **Mutes** - View all mutes with pagination  
- **Warnings** - View all warnings
- **Kicks** - View all kicks
- **Statistics** - View banlist stats
- **Ban Protest** -  How to Submit a Ban Protest
- **Admin** -  Administration area

### Search
- Search by player name or UUID
- Real-time search with auto-suggestions
- View complete punishment history

### Themes
- **Light Theme** - Clean white interface
- **Dark Theme** - Eye-friendly dark interface

### Languages
Switch between supported languages:
- AR العربية
- CS Čeština
- DE Deutsch
- GR Ελληνικά
- EN English
- ES Español
- FR Français
- HU Magyar
- IT Italiano
- JA 日本語
- PL Polski
- RO Română
- RU Русский
- SK Slovenčina
- SR Srpski
- TR Türkçe
- CN 中文 (简体)


## 🐛 Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
- add database to `.env` file

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

## 🛡️ Security Features

- **CSRF Protection** - All forms include CSRF tokens
- **XSS Prevention** - All output is properly escaped
- **SQL Injection Protection** - PDO prepared statements
- **Rate Limiting** - Prevents brute force attacks
- **Secure Sessions** - HTTPOnly, Secure, SameSite cookies
- **Security Headers** - X-Frame-Options, CSP, etc.
- **Input Validation** - Strict input filtering and sanitization


## 📊 Performance Tips

1. **Enable OPcache** in PHP
2. **Use PHP-FPM** instead of mod_php
3. **Enable Gzip compression** (included in .htaccess)
4. **Set up CloudFlare** for CDN and caching
5. **Optimize MySQL** queries and indexes


## 🌟 Roadmap

- [ ] Implement language selection in the admin menu
- [ ] Implement theme color customization
- [ ] Add a comment system for bans
- [ ] Implement Discord and Steam authentication

## 📞 Support

- **GitHub Issues**: [Report bugs or request features](https://github.com/Yamiru/LitebansU/issues)
- **Discord**: [Discord](https://discord.gg/jNVwwcQ)

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Credits

- **Original LiteBans Plugin**: [Ruan](https://www.spigotmc.org/resources/3715/)
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
