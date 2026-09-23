# LitebansU

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/Yamiru/LitebansU.svg)](https://github.com/Yamiru/LitebansU/releases/)
[![GitHub stars](https://img.shields.io/github/stars/Yamiru/LitebansU.svg)](https://github.com/Yamiru/LitebansU/stargazers)

A web interface for the [LiteBans](https://www.spigotmc.org/resources/3715/) punishment plugin. It reads your existing LiteBans database and gives players a public page for bans, mutes, warnings and kicks, and gives staff an admin panel to search, moderate and keep notes.

## Live demo

[https://yamiru.com/litebansU](https://yamiru.com/litebansU)

## Screenshot

![Screenshot](https://i.imgur.com/9DV0RUB.png)

## What's in it

The public side shows paginated, sortable lists of bans, mutes, warnings and kicks, a search by player name or UUID, and a statistics page. It comes in light and dark theme and in 17 languages, and the layout holds up on a phone.

Staff sign in by password, Google, or Discord, and get three roles to work with: administrator, moderator, and viewer, each with less access than the last. The admin panel covers search and moderation, user management, data export and import, cache tools, and an activity log.

### Case Evidence

This is the newest addition. Staff can attach a written report to any ban, mute, warning or kick, along with screenshots, a video, or a demo file, so the next person to look at that punishment doesn't have to ask around or dig through Discord logs. A few things worth knowing about it:

- Uploaded images convert to WebP automatically, and video gets re-encoded to a smaller file if `ffmpeg` is installed.
- Each punishment can also carry a one-line appeal note: pending, accepted, or rejected.
- All of it lives outside the LiteBans database, in its own private folder.
- A staff member can mark a single report public, which shows its text and files on that punishment's page. Player-facing pages never show who wrote it or what the appeal note says, only the report itself.

### SEO and Tracking

Also new. This tab in the admin panel controls whether search engines can crawl the site at all, has a one-click submit to IndexNow, and holds the Google Analytics ID (only loaded after a visitor accepts the cookie notice), an on/off switch for that cookie notice, an editable privacy page in every supported language, and a box for custom head/footer code if you need to add something the panel doesn't cover.

## Requirements

- PHP 8.0 or newer, with `pdo_mysql`, `mbstring`, `intl`, `gd` (built with WebP support), `curl`, `dom` and `openssl`
- MySQL 5.7+ or MariaDB 10.3+
- LiteBans 2.8.0 or newer, already writing to that database
- Apache 2.4+ with `mod_rewrite`, or nginx 1.18+
- Optional: `ffmpeg`, for the video re-encoding mentioned above

## Getting it running

1. Download the [latest release](https://github.com/Yamiru/LitebansU/releases) and extract it into your web folder.
2. Give the web server write access to `data/`, `demos/data/` and `logs/`:
   ```bash
   chmod -R u+rwX data demos/data logs
   ```
3. Open `https://yoursite.com/install.php` and follow the steps, or skip it and copy `.env_example_clean` to `.env` yourself.
4. Delete `install.php` and `hash.php` once the site is up.

Apache reads the `.htaccess` that ships with the project, so there's nothing else to configure there beyond turning on `AllowOverride All` for the folder. nginx doesn't read `.htaccess` at all, so it needs its own rules to keep `config/`, `core/`, `data/` and `demos/data/` from being served directly. Ask if you want an example server block.

## Setting it up

### Database

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password
DB_DRIVER=mysql
TABLE_PREFIX=litebans_
```

### Site

```env
SITE_NAME=LiteBansU
SITE_URL=https://yoursite.com
TIMEZONE=UTC
DEFAULT_THEME=dark
DEFAULT_LANGUAGE=en
```

### Password login

```env
ADMIN_ENABLED=true
ADMIN_PASSWORD=
ALLOW_PASSWORD_LOGIN=true
```

The password itself is a hash, not plain text. Open `https://yoursite.com/hash.php`, type the password you want, and copy the result into `ADMIN_PASSWORD`. Delete `hash.php` once you've done this.

### Google sign-in

```env
GOOGLE_AUTH_ENABLED=true
GOOGLE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret
```

Create the credentials in the [Google Cloud Console](https://console.cloud.google.com/): a new OAuth 2.0 Client ID, with `https://yoursite.com/admin/oauth-callback` set as the redirect URI. Paste the client ID and secret into `.env`.

### Discord sign-in

```env
DISCORD_AUTH_ENABLED=true
DISCORD_CLIENT_ID=your_discord_client_id
DISCORD_CLIENT_SECRET=your_discord_client_secret
```

Create an application in the [Discord Developer Portal](https://discord.com/developers/applications), turn on the `identify` and `email` scopes under OAuth2, and set the redirect URI to `https://yoursite.com/admin/oauth-callback?provider=discord`. Paste the client ID and secret into `.env`.

Both sign-in methods, and the plain password login, are configured in `.env` only; there's no toggle for them in the admin panel itself. Whoever signs in first through Google or Discord becomes administrator. After that, add people from the Users tab by email (for Google) or by their numeric Discord user ID (for Discord: turn on Developer Mode in Discord's settings, then right-click the person and copy their ID). If the `data/` folder isn't writable, sign-in through Google or Discord switches itself off rather than let a stranger claim the first account, and the login page explains why.

### Avatars

Player heads come from [Crafatar](https://crafatar.com) for premium (online-mode) accounts and [Cravatar](https://cravatar.eu) for cracked (offline-mode) accounts by default, since Crafatar can't look up a name-based UUID. Both URLs are editable in Settings if you'd rather use something else.

## Using it

The main navigation covers Home, Bans, Mutes, Warnings, Kicks, Statistics, and Ban Protest, plus Admin if it's enabled. The admin panel itself has Overview, Search and Manage, Case Evidence, SEO and Tracking, Settings, Users, Export/Import and System Info.

An administrator can touch anything. A moderator can view and manage punishments and add case evidence, but not change settings or manage users. A viewer can only look.

### Languages

- Arabic (العربية)
- Czech (Čeština)
- German (Deutsch)
- Greek (Ελληνικά)
- English
- Spanish (Español)
- French (Français)
- Hungarian (Magyar)
- Italian (Italiano)
- Japanese (日本語)
- Polish (Polski)
- Romanian (Română)
- Russian (Русский)
- Slovak (Slovenčina)
- Serbian (Srpski)
- Turkish (Türkçe)
- Chinese, Simplified (中文)

## When something breaks

- **500 error**: check the database credentials in `.env`, then set `DEBUG=true` and look at the PHP error log for the actual message.
- **Every page 404s except the home page**: on Apache, `AllowOverride All` isn't turned on; on nginx, check the `try_files` rule.
- **Admin login fails**: the password hash is wrong, or an OAuth redirect URI doesn't match exactly, or the site isn't running on HTTPS (OAuth requires it).
- **Google or Discord sign-in isn't showing up**: `data/` isn't writable; fix its permissions and reload the page.
- **An evidence upload fails**: raise `upload_max_filesize` and `post_max_size`; the error message tells you the current limit.
- **Switching theme or language does nothing**: clear the browser cache and check that cookies are allowed.

## Keeping it secure

Run it over HTTPS, since OAuth and secure cookies both depend on it. Use a real password for the database and for the admin account. Delete `install.php` and `hash.php` once you're done setting up. Give the database user only the access it needs, not full privileges. Back up the database and `.env` on a schedule you'll actually keep, and check the error log now and then.

## Support

[Issues](https://github.com/Yamiru/LitebansU/issues) for bugs and feature requests, [Discord](https://discord.gg/jNVwwcQ) if you want to ask directly, and the [wiki](https://github.com/Yamiru/LitebansU/wiki) for anything not covered here.

## Contributing

Pull requests are welcome. For anything bigger than a small fix, open an issue first so we can talk about the approach before you put the work in.

## License

MIT. See [LICENSE](LICENSE).

## Credits

LiteBans is by [Ruan](https://www.spigotmc.org/resources/3715/). LitebansU is by [Yamiru](https://github.com/Yamiru). Icons from [Font Awesome](https://fontawesome.com/), avatars from [Crafatar](https://crafatar.com) and [Cravatar](https://cravatar.eu).

---

<div align="center">

[Repository](https://github.com/Yamiru/LitebansU) · [Marketplace](https://builtbybit.com/resources/litebansu-litebans-website.69448) · [Wiki](https://github.com/Yamiru/LitebansU/wiki) · [Issues](https://github.com/Yamiru/LitebansU/issues) · [Discord](https://discord.gg/jNVwwcQ)

</div>
