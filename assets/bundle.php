<?php
/**
 * ============================================================================
 * LiteBansU
 * ============================================================================
 *
 * Plugin Name: LiteBansU
 * Description: A modern, secure, and responsive web interface for LiteBans punishment management system.
 * Version: 5.0
 * Market URI: https://builtbybit.com/resources/litebansu-litebans-website.69448/
 * Author URI: https://yamiru.com
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

$type = ($_GET['t'] ?? '') === 'js' ? 'js' : 'css';
$files = (require __DIR__ . '/bundle-map.php')[$type];

$stamp = '';
foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $stamp .= $file . (is_file($path) ? filemtime($path) . ':' . filesize($path) : 'missing') . '|';
}
$etag = '"' . md5($stamp) . '"';

header('Content-Type: ' . ($type === 'css' ? 'text/css' : 'application/javascript') . '; charset=UTF-8');
header('Cache-Control: public, max-age=31536000, immutable');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
header('ETag: ' . $etag);
header('Vary: Accept-Encoding');
header('X-Content-Type-Options: nosniff');

if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}

$cacheDir = __DIR__ . '/../data/cache';
$cacheFile = $cacheDir . '/bundle-' . $type . '-' . md5($stamp) . '.txt';
$output = is_file($cacheFile) ? (string)file_get_contents($cacheFile) : '';

if ($output === '') {
    foreach ($files as $file) {
        $path = __DIR__ . '/' . $file;
        if (!is_file($path)) {
            continue;
        }
        $source = (string)file_get_contents($path);
        if ($type === 'css') {
            $source = preg_replace('#/\*.*?\*/#s', '', $source);
            $source = preg_replace('/\s+/', ' ', $source);
            $source = preg_replace('/\s*([{};,])\s*/', '$1', $source);
            $source = str_replace(';}', '}', $source);
            $output .= trim($source);
        } else {
            $output .= $source . "\n;\n";
        }
    }
    if (is_dir($cacheDir) || @mkdir($cacheDir, 0755, true)) {
        foreach (glob($cacheDir . '/bundle-' . $type . '-*.txt') ?: [] as $old) {
            @unlink($old);
        }
        @file_put_contents($cacheFile, $output);
    }
}

if (extension_loaded('zlib') && stripos($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip') !== false && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
}
echo $output;
