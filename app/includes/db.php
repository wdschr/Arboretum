<?php
declare(strict_types=1);

/**
 * Works out the directory config.php lives in: a folder called
 * "private_html", sitting next to "public_html", never inside it.
 *
 * Finds the nearest ancestor of the document root that's literally named
 * "public_html", then mirrors however much path sits below that inside
 * "private_html" instead. Two examples:
 *
 *   docroot: /home/sites/example.com/public_html
 *   config:  /home/sites/example.com/private_html/config.php
 *
 *   docroot: /home/sites/example.com/public_html/arboretum   (a
 *            subdomain nested inside the main site - some hosts, e.g.
 *            Heart Internet's Extend panel, do this)
 *   config:  /home/sites/example.com/private_html/arboretum/config.php
 *
 * Either way, config.php sits next to a "public_html" that matches its
 * own path - never loose in the account root, and never anywhere a URL
 * can reach.
 */
function config_dir(): string
{
    $docRoot = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    if ($docRoot === '') {
        return dirname(__DIR__); // local/CLI fallback
    }

    $cursor = $docRoot;
    $subpathParts = [];
    for ($i = 0; $i < 6 && $cursor !== '' && $cursor !== dirname($cursor); $i++) {
        if (basename($cursor) === 'public_html') {
            $accountHome = dirname($cursor);
            $subpath = implode('/', array_reverse($subpathParts));
            return $accountHome . '/private_html' . ($subpath !== '' ? '/' . $subpath : '');
        }
        $subpathParts[] = basename($cursor);
        $cursor = dirname($cursor);
    }

    // No "public_html" ancestor found (unusual layout) - fall back to one
    // level above the document root, same as before.
    return dirname($docRoot);
}

/**
 * Loads config.php, which must live in private_html (see config_dir()
 * above and config.example.php). Never inside public_html.
 */
function get_config(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $candidates = [];
    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $candidates[] = config_dir() . '/config.php';
    }
    // Fallback for local testing with the PHP built-in server, or hosts
    // where DOCUMENT_ROOT isn't set the way we expect.
    $candidates[] = dirname(__DIR__) . '/config.php';
    $candidates[] = dirname(__DIR__, 2) . '/config.php';

    foreach ($candidates as $path) {
        if (is_file($path)) {
            $config = require $path;
            return $config;
        }
    }

    http_response_code(500);
    die('Missing config.php. Copy app/config.example.php to config.php, fill in your database details, and upload it to the matching private_html folder next to public_html. See SETUP.md.');
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $cfg = get_config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $cfg['host'],
        $cfg['name'],
        $cfg['charset'] ?? 'utf8mb4'
    );

    try {
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die('Could not connect to the database. Check the credentials in config.php.');
    }

    return $pdo;
}
