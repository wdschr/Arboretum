<?php
declare(strict_types=1);
/**
 * Include after require_login()/require_role(). Expects these variables
 * to already be set by the including page:
 *   $pageTitle   browser tab title suffix, e.g. "Databases"
 *   $heading     the big h1
 *   $subheading  the paragraph under it
 *   $navActive   one of: hosts, databases, audit, users, account
 *   $statsHtml   optional pre-built .stats markup, default none
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

$siteName = get_config()['site_name'] ?? 'Arboretum';
$user = current_user();
$statsHtml = $statsHtml ?? '';
?><!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title><?= esc($siteName) ?> &middot; <?= esc($pageTitle ?? '') ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="wrap">

<nav class="topnav">
  <a href="index.php" class="<?= $navActive === 'hosts' ? 'active' : '' ?>">Hostnames</a>
  <a href="databases.php" class="<?= $navActive === 'databases' ? 'active' : '' ?>">Databases</a>
  <?php if (in_array($user['role'], ['admin', 'editor'], true)): ?>
    <a href="audit.php" class="<?= $navActive === 'audit' ? 'active' : '' ?>">Audit log</a>
  <?php endif; ?>
  <?php if ($user['role'] === 'admin'): ?>
    <a href="users.php" class="<?= $navActive === 'users' ? 'active' : '' ?>">Users</a>
  <?php endif; ?>
  <span class="spacer"></span>
  <a href="account.php" class="whoami <?= $navActive === 'account' ? 'active' : '' ?>">
    <b><?= esc($user['username']) ?></b> <span class="role-badge"><?= esc($user['role']) ?></span>
  </a>
  <a href="logout.php">Log out</a>
</nav>

<header>
  <div class="mark">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M12 21v-4"/><path d="M12 17 6.5 13h2.6L4.8 9.4h2.9L12 3l4.3 6.4h2.9L14.9 13h2.6L12 17Z"/>
    </svg>
    <?= esc($siteName) ?>
  </div>
  <h1><?= esc($heading ?? '') ?></h1>
  <p class="sub"><?= esc($subheading ?? '') ?></p>
  <?php if ($statsHtml !== ''): ?>
    <div class="stats"><?= $statsHtml /* pre-escaped by caller */ ?></div>
  <?php endif; ?>
</header>

<?php foreach (take_flashes() as $f): ?>
  <div class="flash <?= esc($f['type']) ?>"><?= esc($f['message']) ?></div>
<?php endforeach; ?>
