# LiteBans Modern Web Interface

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/Yamiru/LitebansU.svg)](https://github.com/Yamiru/LitebansU/releases/)
[![GitHub stars](https://img.shields.io/github/stars/Yamiru/LitebansU.svg)](https://github.com/Yamiru/LitebansU/stargazers)

**A modern, secure, and responsive web interface for LiteBans punishment management system.**

---

## 🌐 Live Demo

[https://yamiru.com/litebansU](https://yamiru.com/litebansU)


## Screenshot
![Imgur Image](https://i.imgur.com/YJXukd9.png)


## ✨ Features

### Core Features
- **🎨 Modern UI/UX** - Clean, responsive design with smooth animations and dark/light themes
- **🌍 Multi-language Support** - Arabic, Czech, German, Greek, English, Spanish, French, Hungarian, Italian, Japanese, Polish, Romanian, Russian, Slovak, Serbian, Turkish, Chinese (Simplified) 
- **📈 Statistics Dashboard** - View comprehensive server punishment statistics
- **🔍 Real-time Search** - Instant player punishment search with debouncing
- **📱 Mobile Responsive** - Works perfectly on all devices and screen sizes
- **⚡ Performance Optimized** - Lazy loading, caching, and minimal resource usage
- **🎯 SEO Optimized** - Full SEO meta tags, Schema.org, and Open Graph support
- **🎥 Demo Management** - Advanced Evidence Management System

### 🔐 Authentication & Security
- **🔑 Multiple Login Methods**
  - Traditional password authentication
  - Google OAuth 2.0 integration
  - Discord OAuth 2.0 integration (beta)
- **🛡️ Advanced Security**
  - CSRF protection on all forms
  - XSS prevention with output escaping
  - SQL injection protection via PDO prepared statements
  - Rate limiting to prevent brute force attacks
  - Secure sessions with HTTPOnly, Secure, SameSite cookies
  - Security headers (X-Frame-Options, CSP, etc.)
  - Input validation and sanitization

### 👥 User Management & Permissions
- **Three-tier Permission System**
  - **Administrator** - Full access to all features and settings
  - **Moderator** - View and manage punishments, limited settings access
  - **Viewer** - Read-only access to punishment data
- **OAuth-based User Management**
  - Automatic user registration via OAuth providers
  - Role assignment and management
  - Session management with configurable timeout

### ⚙️ Admin Panel
- **Comprehensive Dashboard** with real-time statistics
- **30+ Configurable Settings** accessible through admin interface
  - Site configuration (name, URL, timezone, pagination)
  - Display options (UUID visibility, silent punishments)
  - Menu visibility controls
  - Contact information (Discord, Email, Forum)
  - SEO settings (meta tags, schema, social media)
  - Security settings (session timeout, rate limiting)
  - Performance settings (cache management)
- **System Management**
  - Real-time configuration reload
  - Cache clearing and management
  - User role management
  - Activity logging
- **Data Export/Import** - Backup and restore punishment data

### 🎨 Customization
- **Theme System**
  - Dark and Light themes
  - Custom theme color configuration
  - Responsive design for all screen sizes
- **Avatar Providers**
  - Crafatar (default)
  - Cravatar
  - Custom avatar URL support
  - Offline player avatar support
- **Menu Configuration**
  - Toggle visibility of Protest, Stats, and Admin menu items
  - Customizable footer information

### 🚀 Installation & Setup
- **Easy Installation Wizard** - Step-by-step setup process
- **Demo Mode** - Test the interface with sample data
- **Demo Installation Script** - Quickly populate database with test punishments
- **Hash Generator** - Built-in tool for generating secure admin passwords

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
   chmod 755 /var/www/html/litebans/data
   ```

4. **Create .htaccess file** (if not included)
   ```bash
   nano /var/www/html/litebans/.htaccess
   ```
   See [.htaccess example](https://github.com/Yamiru/LitebansU/blob/main/.htaccess)

5. **Run installation wizard**
   - Open `https://yoursite.com/install.php` in your browser
   - Follow the step-by-step setup process
   - Or manually configure `.env` file (see below)

## 📋 Requirements

### Server Requirements
- **PHP 8.0+** with extensions:
  - PDO & pdo_mysql
  - mbstring
  - intl
  - gd/imagick
  - curl
  - json
  - session
- **MySQL 5.7+** or **MariaDB 10.3+**
- **Web Server**: Apache 2.4+ (mod_rewrite) or Nginx 1.18+

### LiteBans Plugin
- **LiteBans 2.8.0+** installed on your Minecraft server
- Database access to LiteBans tables

## ⚙️ Configuration

### 1. https://yoursite.com/install.php or edit .env file

Edit `.env` file with your database credentials:

```.env
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password
DB_DRIVER=mysql
TABLE_PREFIX=litebans_
```

### 2. Site Configuration

```.env
# Site Configuration
SITE_NAME=LiteBansU
FOOTER_SITE_NAME=
ITEMS_PER_PAGE=100
TIMEZONE=UTC
DATE_FORMAT=Y-m-d H:i:s
BASE_URL=

# Default Settings
DEFAULT_THEME=dark
DEFAULT_LANGUAGE=en
SHOW_PLAYER_UUID=false
```

### 3. Authentication Configuration

#### Traditional Password Login
```.env
# Admin Configuration
ADMIN_ENABLED=true
ADMIN_PASSWORD=

# Allow password login
ALLOW_PASSWORD_LOGIN=true

```

Generate admin password hash:
1. Open `https://yoursite.com/hash.php`
2. Enter your desired password
3. Copy the generated hash
4. Paste it into `.env` as `ADMIN_PASSWORD`
5. Delete `hash.php` file for security

#### Google OAuth Setup
```.env
# Google OAuth Configuration
GOOGLE_AUTH_ENABLED=true
GOOGLE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret
```

To get Google OAuth credentials:
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `https://yoursite.com/admin/callback/google`

#### Discord OAuth Setup (Beta)
```.env
# Discord OAuth Configuration
DISCORD_AUTH_ENABLED=true
DISCORD_CLIENT_ID=your_discord_client_id
DISCORD_CLIENT_SECRET=your_discord_client_secret
```

To get Discord credentials:
1. Go to [Discord DEV portal](https://discord.com/developers/applications)
2. Create a new APP
3. Add name and create
4. click to OAuth2 and mark identify and email
5. Add redirect URI: `https://yoursite.com/admin/oauth-callback?provider=discord`


## 🎯 Usage

### Navigation
- **Home** - Server statistics and recent activity
- **Bans** - View all bans with pagination
- **Mutes** - View all mutes with pagination  
- **Warnings** - View all warnings
- **Kicks** - View all kicks
- **Statistics** - View detailed banlist statistics
- **Ban Protest** - How to Submit a Ban Protest
- **Admin** - Administration panel (requires authentication)

### Admin Panel Access

#### First Time Setup
1. **Using Password Login:**
   - Generate password hash using `hash.php`
   - Add hash to `.env` as `ADMIN_PASSWORD`
   - Login at `https://yoursite.com/admin`

2. **Using Google OAuth:**
   - Configure Google OAuth in `.env`
   - Visit `https://yoursite.com/admin`
   - Click "Sign in with Google"
   - First user to login becomes Administrator

#### Admin Panel Features
- **Dashboard** - Overview of system status and recent activity
- **Settings** - Modify all configuration options via web interface
- **Users** - Manage user accounts and permissions
- **Cache** - Clear cache and reload configuration
- **System Info** - View PHP, database, and server information

### Permission Levels

- **Administrator**
  - Full access to all features
  - Can modify all settings
  - Can manage users and assign roles
  - Can clear cache and reload config

- **Moderator**
  - View and manage punishments
  - Limited settings access
  - Cannot manage users or system settings

- **Viewer**
  - Read-only access to punishment data
  - Cannot modify any settings
  - Useful for staff members who only need to view punishments

### Search
- Search by player name or UUID
- Real-time search with auto-suggestions
- View complete punishment history
- Filter by punishment type

### Themes
- **Light Theme** - Clean white interface
- **Dark Theme** - Eye-friendly dark interface
- Theme preference saved per user

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
- Verify database credentials in `.env`
- Check file permissions (755 for directories, 644 for files)
- Enable error logging: `DEBUG=true` in `.env`
- Check PHP error logs

#### 2. Admin Login Not Working
- Verify `ADMIN_PASSWORD` hash is correct
- Check if OAuth credentials are properly configured
- Ensure cookies are enabled in browser
- Check session settings in `.env`

#### 3. OAuth Login Fails
- Verify OAuth credentials in `.env`
- Check redirect URIs in OAuth provider settings
- Ensure HTTPS is enabled (required for OAuth)
- Check if OAuth provider API is enabled

#### 4. Theme/Language Switcher Not Working
- Clear browser cache (Ctrl+F5)
- Check JavaScript console for errors
- Verify cookies are enabled
- Ensure `.htaccess` is working (Apache)

#### 5. Search Not Working
- Check CSRF token generation
- Verify JavaScript is enabled
- Check rate limiting settings
- Ensure database permissions

#### 6. Cache Issues
- Clear cache via Admin Panel
- Reload configuration after clearing cache

### File Permissions Check
```bash
# Set correct permissions
find /var/www/html/litebans -type f -exec chmod 644 {} \;
find /var/www/html/litebans -type d -exec chmod 755 {} \;
chmod 755 /var/www/html/litebans/data
```

### Verify Installation
Check that all required files exist:
- `.htaccess`
- `.env`
- `index.php`
- `config/` directory
- `core/` directory
- `controllers/` directory
- `templates/` directory
- `lang/` directory
- `assets/` directory
- `data/` directory (writable)

## 🛡️ Security Best Practices

1. **Always use HTTPS** - Required for OAuth and security
2. **Keep PHP updated** - Use PHP 8.0 or newer
3. **Use strong passwords** - For database and admin accounts
4. **Delete installation files** - Remove `install.php` and `hash.php` after setup
5. **Restrict database access** - Use separate user with minimal permissions
6. **Enable rate limiting** - Protect against brute force attacks
7. **Regular backups** - Backup both database and `.env` file
8. **Monitor logs** - Check error logs regularly
9. **Update regularly** - Keep LiteBansU updated to latest version
10. **Limit OAuth permissions** - Only grant necessary scopes

## 📊 Performance Tips

1. **Enable OPcache** in PHP for better performance
2. **Use PHP-FPM** instead of mod_php
3. **Enable Gzip compression** (included in .htaccess)
4. **Set up CloudFlare** for CDN and caching
5. **Optimize MySQL** queries and indexes
6. **Enable cache** in `.env`: `CACHE_ENABLED=true`
7. **Adjust cache lifetime** based on your needs
8. **Use connection pooling** for high traffic sites

## 🔄 Migration & Updates

### Migrating from Older Versions

1. **Backup everything**
   ```bash
   cp -r /var/www/html/litebans /var/www/html/litebans-backup
   mysqldump -u username -p database_name > litebans_backup.sql
   ```

2. **Extract new version**
   ```bash
   unzip LitebansU-new.zip -d /var/www/html/litebans-new/
   ```

### Updating OAuth Credentials

If you need to change OAuth credentials:
1. Update `.env` file with new credentials
2. Login to Admin Panel
3. Go to Settings → Authentication
4. Update and save
5. Or reload config via Admin Panel → Cache → Reload Config


## 📞 Support

- **GitHub Issues**: [Report bugs or request features](https://github.com/Yamiru/LitebansU/issues)
- **Discord**: [Join our Discord server](https://discord.gg/jNVwwcQ)
- **Documentation**: [Wiki](https://github.com/Yamiru/LitebansU/wiki)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Credits

- **Original LiteBans Plugin**: [Ruan](https://www.spigotmc.org/resources/3715/)
- **Author**: [Yamiru](https://github.com/Yamiru)
- **Icons**: [Font Awesome](https://fontawesome.com/)
- **Fonts**: [Inter](https://rsms.me/inter/) by Rasmus Andersson
- **Cravatar**: [Cravatar](https://cravatar.eu)
- **Crafatar**: [Crafatar](https://crafatar.com)

## 🙏 Special Thanks

- To all contributors and testers
- To the Minecraft community for feedback and support
- To everyone who has starred this repository

## ⭐ Show Your Support

If this project helped you, please consider:
- ⭐ **Starring** the repository
- 🐛 **Reporting bugs** or suggesting features
- 🤝 **Contributing** to the codebase
- 💬 **Sharing** with your community
- ☕ **Supporting** development

---

<div align="center">

**Made with ❤️ for the Minecraft community**

[URL](https://github.com/Yamiru/LitebansU) • [MarketPlace](https://builtbybit.com/resources/litebansu-litebans-website.69448) • [Documentation](https://github.com/Yamiru/LitebansU/wiki) • [Issues](https://github.com/Yamiru/LitebansU/issues) • [Discord](https://discord.gg/jNVwwcQ)


</div>
