<?php
/**
 * ============================================================================
 * LiteBansU
 * ============================================================================
 *
 * Plugin Name:   LiteBansU
 * Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 * Author URI:    https://yamiru.com
 * License:       MIT
 * ============================================================================
 */
declare(strict_types=1);

namespace core;

class TemplateManager
{
    public const DIR = __DIR__ . '/../site-templates';
    public const BUILTIN = ['default', 'bulma', 'frost'];
    private const ID = '/^[a-z0-9][a-z0-9-]{1,30}$/';
    private const ALLOWED_EXT = ['php', 'json', 'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'ico', 'woff', 'woff2', 'ttf', 'txt'];
    private const MAX_ZIP = 8 * 1024 * 1024;
    private const MAX_UNPACKED = 24 * 1024 * 1024;
    private const MAX_FILES = 300;

    private static ?string $active = null;

    public static function isValidId(string $id): bool
    {
        return (bool)preg_match(self::ID, $id) || $id === 'default';
    }

    private static function exists(string $id): bool
    {
        return $id === 'default' || (self::isValidId($id) && is_file(self::DIR . '/' . $id . '/template.json'));
    }

    public static function activeId(array $config): string
    {
        if (self::$active === null) {
            $id = (string)(SiteExtras::get()['template'] ?? '');
            if ($id === '') {
                $id = (string)($config['template'] ?? 'default');
            }
            self::$active = self::exists($id) ? $id : 'default';
        }
        return self::$active;
    }

    public static function has(string $id, string $view): bool
    {
        return $id !== 'default' && self::exists($id) && is_file(self::DIR . '/' . $id . '/' . $view . '.php');
    }

    public static function view(string $id, string $view): string
    {
        return self::has($id, $view) ? self::DIR . '/' . $id . '/' . $view . '.php' : __DIR__ . '/../templates/' . $view . '.php';
    }

    public static function assetUrl(string $id, string $file): string
    {
        return asset('site-templates/' . $id . '/assets/' . ltrim($file, '/'));
    }

    public static function all(string $activeId = ''): array
    {
        $list = ['default' => [
            'id' => 'default', 'name' => 'Default', 'version' => '1.0', 'author' => 'Yamiru',
            'description' => 'The original LiteBansU design (Bootstrap).', 'framework' => 'Bootstrap', 'builtin' => true,
        ]];
        foreach (glob(self::DIR . '/*/template.json') ?: [] as $file) {
            $id = basename(dirname($file));
            if (!self::isValidId($id) || $id === 'default') {
                continue;
            }
            $meta = json_decode((string)@file_get_contents($file), true);
            if (!is_array($meta) || !is_file(dirname($file) . '/header.php') || !is_file(dirname($file) . '/footer.php')) {
                continue;
            }
            $list[$id] = [
                'id' => $id,
                'name' => mb_substr((string)($meta['name'] ?? $id), 0, 60),
                'version' => mb_substr((string)($meta['version'] ?? ''), 0, 20),
                'author' => mb_substr((string)($meta['author'] ?? ''), 0, 60),
                'description' => mb_substr((string)($meta['description'] ?? ''), 0, 200),
                'framework' => mb_substr((string)($meta['framework'] ?? ''), 0, 30),
                'builtin' => in_array($id, self::BUILTIN, true),
            ];
        }
        foreach ($list as $id => &$t) {
            $t['active'] = $id === $activeId;
        }
        return array_values($list);
    }

    public static function install(string $zipPath): array
    {
        if (!class_exists(\ZipArchive::class)) {
            return [false, 'The PHP zip extension is not installed.'];
        }
        if (filesize($zipPath) > self::MAX_ZIP) {
            return [false, 'The zip is larger than ' . (self::MAX_ZIP >> 20) . ' MB.'];
        }
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return [false, 'This is not a valid zip file.'];
        }
        if ($zip->numFiles < 1 || $zip->numFiles > self::MAX_FILES) {
            return [false, 'The zip must contain 1 to ' . self::MAX_FILES . ' files.'];
        }

        $root = null;
        $size = 0;
        $files = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $name = (string)$stat['name'];
            if (str_ends_with($name, '/')) {
                continue;
            }
            if ($name === '' || str_contains($name, '..') || str_contains($name, '\\') || str_contains($name, "\0") || $name[0] === '/' || preg_match('/^[A-Za-z]:/', $name)) {
                return [false, 'Unsafe file name in the zip: ' . mb_substr($name, 0, 60)];
            }
            $size += (int)$stat['size'];
            if ($size > self::MAX_UNPACKED) {
                return [false, 'The unpacked template is larger than ' . (self::MAX_UNPACKED >> 20) . ' MB.'];
            }
            $files[$i] = $name;
            if (basename($name) === 'template.json' && substr_count($name, '/') <= 1 && $root === null) {
                $root = dirname($name) === '.' ? '' : dirname($name) . '/';
            }
        }
        if ($root === null) {
            return [false, 'template.json was not found in the zip.'];
        }
        $meta = json_decode((string)$zip->getFromName($root . 'template.json'), true);
        $id = is_array($meta) ? (string)($meta['id'] ?? '') : '';
        if (!preg_match(self::ID, $id) || in_array($id, self::BUILTIN, true)) {
            return [false, 'template.json needs an "id" of 2-31 lowercase letters, digits or dashes (not default, bulma or frost).'];
        }
        $names = [];
        foreach ($files as $i => $name) {
            if (strncmp($name, $root, strlen($root)) !== 0) {
                return [false, 'All files must be inside the same folder as template.json.'];
            }
            $rel = substr($name, strlen($root));
            if (!preg_match('#^[A-Za-z0-9_.\-/]+$#', $rel) || !in_array(strtolower(pathinfo($rel, PATHINFO_EXTENSION)), self::ALLOWED_EXT, true)) {
                return [false, 'File type not allowed in a template: ' . mb_substr($rel, 0, 60)];
            }
            $names[$i] = $rel;
        }
        if (!in_array('header.php', $names, true) || !in_array('footer.php', $names, true)) {
            return [false, 'A template needs header.php and footer.php.'];
        }

        $tmp = self::DIR . '/.tmp-' . bin2hex(random_bytes(6));
        if (!@mkdir($tmp, 0750, true)) {
            return [false, 'Cannot write to site-templates/ (check permissions).'];
        }
        foreach ($names as $i => $rel) {
            $target = $tmp . '/' . $rel;
            if (!is_dir(dirname($target))) {
                @mkdir(dirname($target), 0750, true);
            }
            $in = $zip->getStream($files[$i]);
            $out = $in ? @fopen($target, 'wb') : false;
            if (!$in || !$out) {
                self::rmdir($tmp);
                return [false, 'Cannot unpack ' . $rel . '.'];
            }
            stream_copy_to_stream($in, $out);
            fclose($in);
            fclose($out);
        }
        $zip->close();

        $final = self::DIR . '/' . $id;
        if (is_dir($final)) {
            self::rmdir($final);
        }
        if (!@rename($tmp, $final)) {
            self::rmdir($tmp);
            return [false, 'Cannot save the template (check permissions of site-templates/).'];
        }
        return [true, 'Template "' . $id . '" installed.'];
    }

    public static function delete(string $id): bool
    {
        if (!preg_match(self::ID, $id) || in_array($id, self::BUILTIN, true) || !is_dir(self::DIR . '/' . $id)) {
            return false;
        }
        self::rmdir(self::DIR . '/' . $id);
        return true;
    }

    private static function rmdir(string $dir): void
    {
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $f) {
            $f->isDir() && !$f->isLink() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
        }
        @rmdir($dir);
    }
}
