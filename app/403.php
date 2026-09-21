<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_config()['site_name'] ?? 'Arboretum';
?><!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= esc($siteName) ?> &middot; Not allowed</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="wrap">
  <div class="auth-wrap">
    <div class="auth-card">
      <h1>Not allowed</h1>
      <p class="sub">Your account doesn't have permission to see this page.</p>
      <a class="btn" href="index.php">Back to the directory</a>
    </div>
  </div>
</div>
</body>
</html>
