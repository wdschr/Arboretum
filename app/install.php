<?php
declare(strict_types=1);

/**
 * One-time GUI installer, in the spirit of Roundcube's installer/index.php:
 * fill in your database details, it tests the connection, creates the
 * tables, and writes config.php to the right place for you. If it can't
 * write the file itself (some hosts won't let PHP write outside the web
 * root), it shows you the finished file to copy up by hand instead.
 *
 * Delete this file from the server once config.php exists - it refuses to
 * run a second time, but there's no reason to leave it reachable.
 */

const REQUIRED_PHP_VERSION = '8.1.0';

/** Kept in sync with db/schema.sql by hand - update both if either changes. */
function schema_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS users (
          id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          username           VARCHAR(50) NOT NULL UNIQUE,
          password_hash      VARCHAR(255) NOT NULL,
          role               ENUM('admin','editor','viewer') NOT NULL DEFAULT 'viewer',
          active             TINYINT(1) NOT NULL DEFAULT 1,
          must_change_password TINYINT(1) NOT NULL DEFAULT 0,
          failed_attempts    TINYINT UNSIGNED NOT NULL DEFAULT 0,
          locked_until       DATETIME NULL,
          created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          last_login_at      DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS hosts (
          id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          hostname     VARCHAR(100) NOT NULL UNIQUE,
          tier         ENUM('machine','webapp','service') NOT NULL,
          role         VARCHAR(150) NOT NULL DEFAULT '',
          runs         VARCHAR(150) NOT NULL DEFAULT '',
          status       ENUM('live','planned') NOT NULL DEFAULT 'live',
          ip           VARCHAR(45) NOT NULL DEFAULT '',
          port         VARCHAR(20) NOT NULL DEFAULT '',
          on_machine   VARCHAR(100) NOT NULL DEFAULT '',
          why          VARCHAR(255) NOT NULL DEFAULT '',
          notes        VARCHAR(255) NOT NULL DEFAULT '',
          created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          created_by   INT UNSIGNED NULL,
          updated_by   INT UNSIGNED NULL,
          FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
          FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS databases_directory (
          id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          name         VARCHAR(100) NOT NULL UNIQUE,
          engine_name  VARCHAR(50) NOT NULL DEFAULT '',
          host_machine VARCHAR(100) NOT NULL DEFAULT '',
          port         VARCHAR(20) NOT NULL DEFAULT '',
          purpose      VARCHAR(150) NOT NULL DEFAULT '',
          status       ENUM('live','planned') NOT NULL DEFAULT 'live',
          notes        VARCHAR(255) NOT NULL DEFAULT '',
          created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          created_by   INT UNSIGNED NULL,
          updated_by   INT UNSIGNED NULL,
          FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
          FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS audit_log (
          id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          user_id      INT UNSIGNED NULL,
          username     VARCHAR(50) NOT NULL,
          action       ENUM('create','update','delete') NOT NULL,
          entity_type  VARCHAR(20) NOT NULL,
          entity_label VARCHAR(150) NOT NULL,
          details      TEXT NULL,
          created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];
}

/**
 * Where to write config.php: inside a "private_html" folder sitting next
 * to "public_html", mirroring however much path sits below the nearest
 * "public_html" ancestor of the document root. Kept in sync by hand with
 * the matching config_dir() in includes/db.php - see the comment there
 * for the two worked examples.
 */
function config_target_path(): string
{
    $docRoot = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    if ($docRoot === '') {
        return dirname(__DIR__) . '/config.php';
    }

    $cursor = $docRoot;
    $subpathParts = [];
    for ($i = 0; $i < 6 && $cursor !== '' && $cursor !== dirname($cursor); $i++) {
        if (basename($cursor) === 'public_html') {
            $accountHome = dirname($cursor);
            $subpath = implode('/', array_reverse($subpathParts));
            return $accountHome . '/private_html' . ($subpath !== '' ? '/' . $subpath : '') . '/config.php';
        }
        $subpathParts[] = basename($cursor);
        $cursor = dirname($cursor);
    }

    return dirname($docRoot) . '/config.php';
}

function build_config_source(array $v): string
{
    // var_export() on each value keeps this safe against quote/backslash
    // injection - nothing from the form is ever concatenated raw into code.
    $e = fn($val) => var_export($val, true);
    return "<?php\nreturn [\n"
        . "    'db' => [\n"
        . "        'host'    => {$e($v['db_host'])},\n"
        . "        'name'    => {$e($v['db_name'])},\n"
        . "        'user'    => {$e($v['db_user'])},\n"
        . "        'pass'    => {$e($v['db_pass'])},\n"
        . "        'charset' => 'utf8mb4',\n"
        . "    ],\n"
        . "    'site_name' => {$e($v['site_name'])},\n"
        . "    'domain' => {$e($v['domain'])},\n"
        . "    'setup_key' => {$e($v['setup_key'])},\n"
        . "];\n";
}

$targetPath = config_target_path();
$alreadyConfigured = is_file($targetPath);

$envChecks = [
    ['label' => 'PHP version ' . REQUIRED_PHP_VERSION . '+', 'ok' => version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '>=') , 'detail' => 'Running ' . PHP_VERSION],
    ['label' => 'pdo_mysql extension', 'ok' => extension_loaded('pdo_mysql'), 'detail' => ''],
    ['label' => 'session extension', 'ok' => extension_loaded('session'), 'detail' => ''],
    ['label' => 'random_bytes() available', 'ok' => function_exists('random_bytes'), 'detail' => ''],
];
$envOk = !in_array(false, array_column($envChecks, 'ok'), true);

$errors = [];
$result = null; // set after a successful/failed submit
$values = [
    'db_host'    => 'localhost',
    'db_name'    => '',
    'db_user'    => '',
    'db_pass'    => '',
    'site_name'  => 'Arboretum',
    'domain'     => 'example.com',
    'setup_key'  => bin2hex(random_bytes(20)),
];

if (!$alreadyConfigured && $envOk && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $default) {
        $values[$key] = trim((string)($_POST[$key] ?? $default));
    }
    if ($values['setup_key'] === '') {
        $errors[] = 'Setup key can\'t be empty - you need it to run setup.php next.';
    }
    if ($values['db_name'] === '') $errors[] = 'Database name is required.';
    if ($values['db_user'] === '') $errors[] = 'Database username is required.';
    if ($values['site_name'] === '') $errors[] = 'Site name is required.';
    if ($values['domain'] === '') $errors[] = 'Domain is required.';

    $pdo = null;
    if (!$errors) {
        try {
            $dsn = sprintf('mysql:host=%s;charset=utf8mb4', $values['db_host']);
            $pdo = new PDO($dsn, $values['db_user'], $values['db_pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->exec('USE `' . str_replace('`', '', $values['db_name']) . '`');
        } catch (PDOException $e) {
            $errors[] = 'Could not connect: ' . $e->getMessage();
        }
    }

    $tableStatus = [];
    if (!$errors && $pdo) {
        foreach (schema_statements() as $sql) {
            preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $sql, $m);
            $table = $m[1] ?? '?';
            try {
                $pdo->exec($sql);
                $tableStatus[$table] = true;
            } catch (PDOException $e) {
                $tableStatus[$table] = $e->getMessage();
                $errors[] = "Could not create table \"$table\": " . $e->getMessage();
            }
        }
    }

    if (!$errors) {
        $source = build_config_source($values);
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0750, true);
        }
        $written = false;
        if (is_dir($targetDir) && is_writable($targetDir)) {
            $written = @file_put_contents($targetPath, $source, LOCK_EX) !== false;
            if ($written) {
                @chmod($targetPath, 0640);
            }
        }
        $result = [
            'written'      => $written,
            'targetPath'   => $targetPath,
            'source'       => $source,
            'tableStatus'  => $tableStatus,
        ];
    }
}
?><!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Install &middot; Arboretum</title>
<link rel="stylesheet" href="assets/style.css">
<style>
  .install-wrap { max-width: 640px; margin: 40px auto; }
  .env-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .env-table td { padding: 6px 4px; font-size: 13.5px; border-bottom: 1px solid var(--line); }
  .ok { color: var(--live); font-weight: 650; }
  .bad { color: var(--danger); font-weight: 650; }
  textarea.code { font-family: var(--mono); font-size: 12.5px; width: 100%; min-height: 260px; }
  .path-box { font-family: var(--mono); font-size: 12.5px; background: var(--panel-2); border: 1px solid var(--line); border-radius: 8px; padding: 8px 10px; word-break: break-all; }
</style>
</head>
<body>
<div class="wrap install-wrap">
  <div class="mark">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M12 21v-4"/><path d="M12 17 6.5 13h2.6L4.8 9.4h2.9L12 3l4.3 6.4h2.9L14.9 13h2.6L12 17Z"/>
    </svg>
    Arboretum
  </div>
  <h1>Install</h1>

<?php if ($alreadyConfigured): ?>
  <div class="flash notice">A config.php already exists at <code><?= htmlspecialchars($targetPath, ENT_QUOTES) ?></code>. This installer won't overwrite it.</div>
  <p>If you need to start over, delete that file yourself first (FTP or File Manager), then reload this page. Otherwise:</p>
  <p><a class="btn btn-primary" href="setup.php">Continue to setup.php &rarr;</a></p>

<?php elseif ($result): ?>

  <?php if ($result['written']): ?>
    <div class="flash success">config.php written to <span class="path-box"><?= htmlspecialchars($result['targetPath'], ENT_QUOTES) ?></span>.</div>
  <?php else: ?>
    <div class="flash error">Couldn't write the file automatically (PHP doesn't have permission to write there). Copy the text below and save it yourself.</div>
    <p>Upload it as <strong>config.php</strong> to:</p>
    <div class="path-box"><?= htmlspecialchars($result['targetPath'], ENT_QUOTES) ?></div>
    <p><textarea class="code" readonly onclick="this.select()"><?= htmlspecialchars($result['source'], ENT_QUOTES) ?></textarea></p>
  <?php endif; ?>

  <table class="env-table">
    <?php foreach ($result['tableStatus'] as $table => $status): ?>
      <tr><td><?= htmlspecialchars($table, ENT_QUOTES) ?> table</td>
          <td class="<?= $status === true ? 'ok' : 'bad' ?>"><?= $status === true ? 'Ready' : htmlspecialchars((string)$status, ENT_QUOTES) ?></td></tr>
    <?php endforeach; ?>
  </table>

  <p><strong>Delete install.php from the server now</strong> - it has no password of its own to protect it, so there's no reason to leave it reachable once config.php exists.</p>
  <p><a class="btn btn-primary" href="setup.php">Continue to setup.php &rarr;</a></p>

<?php else: ?>

  <p class="sub">Fill this in once to create <code>config.php</code> and set up the database tables. Nothing is saved until you submit.</p>

  <table class="env-table">
    <?php foreach ($envChecks as $c): ?>
      <tr><td><?= htmlspecialchars($c['label'], ENT_QUOTES) ?></td>
          <td class="<?= $c['ok'] ? 'ok' : 'bad' ?>"><?= $c['ok'] ? 'OK' : 'Missing' ?><?= $c['detail'] ? ' (' . htmlspecialchars($c['detail'], ENT_QUOTES) . ')' : '' ?></td></tr>
    <?php endforeach; ?>
  </table>

  <?php if (!$envOk): ?>
    <div class="flash error">Your hosting's PHP doesn't meet the requirements above. Ask your host to enable the missing extension or switch PHP version in MultiPHP Manager, then reload this page.</div>
  <?php else: ?>

    <?php foreach ($errors as $e): ?>
      <div class="flash error"><?= htmlspecialchars($e, ENT_QUOTES) ?></div>
    <?php endforeach; ?>

    <form method="post" class="form-card" style="padding:0;border:none;box-shadow:none;max-width:none;">
      <div class="form-grid">
        <div class="field">
          <label for="db_host">Database host</label>
          <input type="text" id="db_host" name="db_host" value="<?= htmlspecialchars($values['db_host'], ENT_QUOTES) ?>" required>
        </div>
        <div class="field">
          <label for="db_name">Database name</label>
          <input type="text" id="db_name" name="db_name" value="<?= htmlspecialchars($values['db_name'], ENT_QUOTES) ?>" required>
        </div>
        <div class="field">
          <label for="db_user">Database username</label>
          <input type="text" id="db_user" name="db_user" value="<?= htmlspecialchars($values['db_user'], ENT_QUOTES) ?>" required>
        </div>
        <div class="field">
          <label for="db_pass">Database password</label>
          <input type="password" id="db_pass" name="db_pass" value="<?= htmlspecialchars($values['db_pass'], ENT_QUOTES) ?>">
        </div>
        <div class="field">
          <label for="site_name">App name</label>
          <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($values['site_name'], ENT_QUOTES) ?>" required>
          <span class="hint">Shown in the page title and header.</span>
        </div>
        <div class="field">
          <label for="domain">Domain</label>
          <input type="text" id="domain" name="domain" value="<?= htmlspecialchars($values['domain'], ENT_QUOTES) ?>" required>
          <span class="hint">Hostnames get appended to this, e.g. "app1" becomes app1.<?= htmlspecialchars($values['domain'], ENT_QUOTES) ?>.</span>
        </div>
        <div class="field full">
          <label for="setup_key">Setup key</label>
          <input type="text" id="setup_key" name="setup_key" value="<?= htmlspecialchars($values['setup_key'], ENT_QUOTES) ?>" required style="font-family:var(--mono);font-size:12.5px;">
          <span class="hint">Random by default - copy this now, you'll need it on the next page (setup.php) to create your admin account.</span>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Test connection &amp; create config.php</button>
      </div>
    </form>

  <?php endif; ?>
<?php endif; ?>

</div>
</body>
</html>
