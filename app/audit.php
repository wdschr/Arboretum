<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/audit.php';

require_role(['admin', 'editor']);
$log = recent_audit_log(200);

$pageTitle = 'Audit log';
$heading = 'Audit log';
$subheading = 'Every add, edit and delete across hostnames, databases and users. Most recent 200 shown.';
$navActive = 'audit';
require __DIR__ . '/includes/layout_top.php';
?>

<div class="table-wrap">
  <table class="data">
    <thead>
      <tr>
        <th>When</th>
        <th>Who</th>
        <th>Action</th>
        <th>Type</th>
        <th>Entry</th>
        <th>Details</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$log): ?>
        <tr><td colspan="6">Nothing logged yet.</td></tr>
      <?php endif; ?>
      <?php foreach ($log as $row): ?>
      <tr>
        <td><?= esc(date('j M Y, H:i', strtotime($row['created_at']))) ?></td>
        <td><?= esc($row['username']) ?></td>
        <td><?= esc(ucfirst($row['action'])) ?></td>
        <td><?= esc($row['entity_type']) ?></td>
        <td><?= esc($row['entity_label']) ?></td>
        <td class="wrap-cell"><?= esc($row['details'] ?? '') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
