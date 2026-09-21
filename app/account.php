<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/audit.php';
require_once __DIR__ . '/includes/password_policy.php';

$user = require_login();
$pdo = db();
$errors = [];
$forced = !empty($user['must_change_password']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $current  = (string)($_POST['current_password'] ?? '');
    $newPass  = (string)($_POST['new_password'] ?? '');
    $confirm  = (string)($_POST['new_password_confirm'] ?? '');

    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
    $stmt->execute([$user['id']]);
    $hash = $stmt->fetchColumn();

    if (!$hash || !password_verify($current, $hash)) {
        $errors[] = 'Current password is wrong.';
    }
    if ($newPass !== $confirm) {
        $errors[] = 'New passwords do not match.';
    }
    $errors = array_merge($errors, password_policy_errors($newPass, $user['username']));

    if (!$errors) {
        $pdo->prepare('UPDATE users SET password_hash = ?, must_change_password = 0 WHERE id = ?')
            ->execute([password_hash($newPass, PASSWORD_DEFAULT), $user['id']]);
        log_action('update', 'user', $user['username'], 'password changed by self');
        refresh_current_user();
        set_flash('success', 'Password updated.');
        redirect('index.php');
    }
}

$pageTitle = 'Account';
$heading = 'Your account';
$subheading = 'Change your own password.';
$navActive = 'account';
require __DIR__ . '/includes/layout_top.php';
?>

<?php if ($forced): ?>
  <div class="flash notice">An admin reset your password. Choose a new one to continue.</div>
<?php endif; ?>
<?php foreach ($errors as $e): ?>
  <div class="flash error"><?= esc($e) ?></div>
<?php endforeach; ?>

<div class="form-card">
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field full">
        <label for="current_password">Current password</label>
        <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
      </div>
      <div class="field">
        <label for="new_password">New password</label>
        <input type="password" id="new_password" name="new_password" autocomplete="new-password" required minlength="<?= PASSWORD_MIN_LENGTH ?>">
        <span class="hint">At least <?= PASSWORD_MIN_LENGTH ?> characters. NCSC's advice: three random unrelated words beats a short word with a number stuck on the end.</span>
      </div>
      <div class="field">
        <label for="new_password_confirm">Confirm new password</label>
        <input type="password" id="new_password_confirm" name="new_password_confirm" autocomplete="new-password" required minlength="<?= PASSWORD_MIN_LENGTH ?>">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Update password</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
