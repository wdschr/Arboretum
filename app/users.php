<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

$me = require_role(['admin']);
$users = db()->query('SELECT * FROM users ORDER BY username')->fetchAll();

$pageTitle = 'Users';
$heading = 'Users';
$subheading = 'Everyone with an account. Admins manage users and delete entries; editors add and edit; viewers only look.';
$navActive = 'users';
require __DIR__ . '/includes/layout_top.php';
?>

<div class="controls" id="controls" style="position:static;">
  <span class="spacer"></span>
  <a class="btn btn-primary" href="user-form.php">+ Add user</a>
</div>

<div class="table-wrap">
  <table class="data">
    <thead>
      <tr>
        <th></th>
        <th>Username</th>
        <th>Role</th>
        <th>Last login</th>
        <th>Created</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><span class="status-dot <?= $u['active'] ? 'active' : 'inactive' ?>" title="<?= $u['active'] ? 'Active' : 'Deactivated' ?>"></span></td>
        <td><?= esc($u['username']) ?><?= (int)$u['id'] === (int)$me['id'] ? ' <span class="hint">(you)</span>' : '' ?></td>
        <td><span class="role-pill <?= esc($u['role']) ?>"><?= esc($u['role']) ?></span></td>
        <td><?= $u['last_login_at'] ? esc(date('j M Y, H:i', strtotime($u['last_login_at']))) : '—' ?></td>
        <td><?= esc(date('j M Y', strtotime($u['created_at']))) ?></td>
        <td class="wrap-cell">
          <a class="btn-link" href="user-form.php?id=<?= (int)$u['id'] ?>">Edit</a>
          &nbsp;&middot;&nbsp;
          <form method="post" action="user-password.php" style="display:inline" onsubmit="return confirm('Reset the password for <?= esc(addslashes($u['username'])) ?>? A new temporary password will be shown once.');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <button type="submit" class="btn-link">Reset password</button>
          </form>
          <?php if ((int)$u['id'] !== (int)$me['id']): ?>
          &nbsp;&middot;&nbsp;
          <form method="post" action="user-delete.php" style="display:inline" onsubmit="return confirm('Delete <?= esc(addslashes($u['username'])) ?>? This can\'t be undone - their account will no longer exist, though their past actions stay in the audit log.');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <button type="submit" class="btn-link danger">Delete</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php
$footerHtml = '<p>Deactivating a user (via Edit) keeps their account but blocks login. Deleting removes the account entirely - either way, their past actions stay in the audit log under their old username.</p>';
require __DIR__ . '/includes/layout_bottom.php';
?>
