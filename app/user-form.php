<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/audit.php';

$me = require_role(['admin']);
$pdo = db();

$id = get_int('id', 0);
$isEdit = $id > 0;
$existing = null;
$tempPassword = null;

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) {
        set_flash('error', 'That user no longer exists.');
        redirect('users.php');
    }
}

$errors = [];
$form = [
    'username' => $existing['username'] ?? '',
    'role'     => $existing['role'] ?? 'viewer',
    'active'   => $existing['active'] ?? 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $form['role']   = post_string('role');
    $form['active'] = isset($_POST['active']) ? 1 : 0;

    if (!in_array($form['role'], ['admin', 'editor', 'viewer'], true)) {
        $errors[] = 'Pick a valid role.';
    }

    if ($isEdit && (int)$existing['id'] === (int)$me['id'] && ($form['role'] !== 'admin' || $form['active'] !== 1)) {
        $errors[] = "You can't remove your own admin role or deactivate your own account. Ask another admin.";
    }

    if (!$isEdit) {
        $form['username'] = post_string('username');
        if ($form['username'] === '' || !preg_match('/^[a-zA-Z0-9_.-]{2,50}$/', $form['username'])) {
            $errors[] = 'Username must be 2-50 characters: letters, numbers, dot, dash or underscore.';
        }
    }

    if (!$errors) {
        try {
            if ($isEdit) {
                $pdo->prepare('UPDATE users SET role = ?, active = ? WHERE id = ?')
                    ->execute([$form['role'], $form['active'], $id]);
                log_action('update', 'user', $form['username'], "role: {$form['role']}, active: {$form['active']}");
                set_flash('success', 'Updated ' . $form['username'] . '.');
                redirect('users.php');
            } else {
                $tempPassword = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 12);
                $stmt = $pdo->prepare(
                    'INSERT INTO users (username, password_hash, role, active, must_change_password) VALUES (?, ?, ?, 1, 1)'
                );
                $stmt->execute([$form['username'], password_hash($tempPassword, PASSWORD_DEFAULT), $form['role']]);
                log_action('create', 'user', $form['username'], "role: {$form['role']}");
                set_flash('success', 'Created ' . $form['username'] . '. Temporary password: ' . $tempPassword . ' - give this to them now, it will not be shown again. They must change it at first login.');
                redirect('users.php');
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'That username is already taken.';
            } else {
                throw $e;
            }
        }
    }
}

$pageTitle = $isEdit ? 'Edit user' : 'Add user';
$heading = $isEdit ? 'Edit ' . $existing['username'] : 'Add a user';
$subheading = $isEdit ? 'Change role or access.' : 'Create an account. A temporary password is generated for you to hand over.';
$navActive = 'users';
require __DIR__ . '/includes/layout_top.php';
?>

<?php foreach ($errors as $e): ?>
  <div class="flash error"><?= esc($e) ?></div>
<?php endforeach; ?>

<div class="form-card">
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="form-grid">
      <?php if (!$isEdit): ?>
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= esc($form['username']) ?>" required autofocus>
      </div>
      <?php else: ?>
      <div class="field">
        <label>Username</label>
        <input type="text" value="<?= esc($form['username']) ?>" disabled>
      </div>
      <?php endif; ?>
      <div class="field">
        <label for="role">Role</label>
        <select id="role" name="role" required>
          <option value="admin" <?= $form['role'] === 'admin' ? 'selected' : '' ?>>Admin - full access, manages users</option>
          <option value="editor" <?= $form['role'] === 'editor' ? 'selected' : '' ?>>Editor - can add/edit entries</option>
          <option value="viewer" <?= $form['role'] === 'viewer' ? 'selected' : '' ?>>Viewer - read only</option>
        </select>
      </div>
      <?php if ($isEdit): ?>
      <div class="field">
        <label for="active">
          <input type="checkbox" id="active" name="active" value="1" <?= $form['active'] ? 'checked' : '' ?> style="width:auto;display:inline-block;margin-right:6px;">
          Active (can log in)
        </label>
      </div>
      <?php endif; ?>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save changes' : 'Create user' ?></button>
      <a class="btn" href="users.php">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
