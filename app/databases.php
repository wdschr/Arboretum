<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

$user = require_login();

$dbs = db()->query('SELECT * FROM databases_directory ORDER BY status DESC, name')->fetchAll();

$byStatus = fn(string $s) => count(array_filter($dbs, fn($d) => $d['status'] === $s));

$statsHtml =
      '<span class="stat"><b>' . count($dbs) . '</b> databases</span>'
    . '<span class="stat"><b>' . $byStatus('live') . '</b> live</span>'
    . '<span class="stat"><b>' . $byStatus('planned') . '</b> planned</span>';

$pageTitle = 'Database directory';
$heading = 'Database directory';
$subheading = 'Every database on your network, what engine it runs, where it lives, and what uses it. No credentials are stored here.';
$navActive = 'databases';
require __DIR__ . '/includes/layout_top.php';
?>

<div class="controls" id="controls">
  <input type="search" id="q" placeholder="Search name, engine, purpose, notes…" autocomplete="off" spellcheck="false" aria-label="Search the database directory">
  <div class="filters" id="statusFilters">
    <span class="flabel">Status</span>
    <button class="chip" type="button" data-status="all" aria-pressed="true">All</button>
    <button class="chip" type="button" data-status="live" aria-pressed="false">Live</button>
    <button class="chip" type="button" data-status="planned" aria-pressed="false">Planned</button>
  </div>
  <span class="spacer"></span>
  <?php if (can_edit()): ?>
    <a class="btn btn-primary" href="database-form.php">+ Add database</a>
  <?php endif; ?>
</div>

<main id="out"></main>
<p class="empty" id="empty">Nothing matches that search.</p>

<?php
$footerHtml = '<p>Track connection details here if it helps, never passwords. Status values are <code>live</code> or <code>planned</code>.</p>'
    . '<p>' . (can_edit() ? 'Use "Add database" above to add or edit entries.' : 'Ask an admin or editor to add or change entries.') . '</p>';
require __DIR__ . '/includes/layout_bottom.php';
?>

<script>
const DATABASES = <?= json_for_script($dbs) ?>;
const CAN_EDIT = <?= json_encode(can_edit()) ?>;
const CAN_DELETE = <?= json_encode(can_delete()) ?>;
const CSRF_TOKEN = <?= json_encode(csrf_token()) ?>;
</script>
<script src="assets/common.js"></script>
<script src="assets/databases.js"></script>
