<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

$user = require_login();

$hosts = db()->query('SELECT * FROM hosts ORDER BY tier, hostname')->fetchAll();

$byTier = fn(string $t) => count(array_filter($hosts, fn($h) => $h['tier'] === $t));
$byStatus = fn(string $s) => count(array_filter($hosts, fn($h) => $h['status'] === $s));

$statsHtml =
      '<span class="stat"><b>' . count($hosts) . '</b> names</span>'
    . '<span class="stat"><b>' . $byTier('machine') . '</b> machines</span>'
    . '<span class="stat"><b>' . $byTier('webapp') . '</b> web apps</span>'
    . '<span class="stat"><b>' . $byTier('service') . '</b> services</span>'
    . '<span class="stat"><b>' . $byStatus('live') . '</b> live</span>'
    . '<span class="stat"><b>' . $byStatus('planned') . '</b> planned</span>';

$pageTitle = 'Hostname directory';
$heading = 'Hostname directory';
$subheading = 'Every machine, web app and service on your network, what each one is called, and what each one runs.';
$navActive = 'hosts';
require __DIR__ . '/includes/layout_top.php';
?>

<div class="controls" id="controls">
  <input type="search" id="q" placeholder="Search name, role, software, notes…" autocomplete="off" spellcheck="false" aria-label="Search the directory">
  <div class="filters" id="tierFilters">
    <span class="flabel">Tier</span>
    <button class="chip" type="button" data-tier="all" aria-pressed="true">All</button>
    <button class="chip" type="button" data-tier="machine" aria-pressed="false">Machines</button>
    <button class="chip" type="button" data-tier="webapp" aria-pressed="false">Web apps</button>
    <button class="chip" type="button" data-tier="service" aria-pressed="false">Services</button>
  </div>
  <span class="spacer"></span>
  <div class="filters" id="statusFilters">
    <span class="flabel">Status</span>
    <button class="chip" type="button" data-status="all" aria-pressed="true">All</button>
    <button class="chip" type="button" data-status="live" aria-pressed="false">Live</button>
    <button class="chip" type="button" data-status="planned" aria-pressed="false">Planned</button>
  </div>
  <?php if (can_edit()): ?>
    <a class="btn btn-primary" href="host-form.php">+ Add hostname</a>
  <?php endif; ?>
</div>

<main id="out"></main>
<p class="empty" id="empty">Nothing matches that search.</p>

<?php
$footerHtml = '<p>Status values are <code>live</code> or <code>planned</code>. Tier values are <code>machine</code>, <code>webapp</code> or <code>service</code>.</p>'
    . '<p>' . (can_edit() ? 'Use "Add hostname" above to add or edit entries.' : 'Ask an admin or editor to add or change entries.') . '</p>';
require __DIR__ . '/includes/layout_bottom.php';
?>

<script>
const DOMAIN = <?= json_encode(get_config()['domain'] ?? 'example.com') ?>;
const SERVERS = <?= json_for_script($hosts) ?>;
const CAN_EDIT = <?= json_encode(can_edit()) ?>;
const CAN_DELETE = <?= json_encode(can_delete()) ?>;
const CSRF_TOKEN = <?= json_encode(csrf_token()) ?>;
</script>
<script src="assets/common.js"></script>
<script src="assets/hosts.js"></script>
