<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/audit.php';
require_once __DIR__ . '/includes/suggestions.php';

$user = require_role(['admin', 'editor']);
$pdo = db();

$id = get_int('id', 0);
$isEdit = $id > 0;
$existing = null;

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM hosts WHERE id = ?');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) {
        set_flash('error', 'That hostname no longer exists.');
        redirect('index.php');
    }
}

$errors = [];
$form = $existing ?? [
    'hostname' => '', 'tier' => 'webapp', 'role' => '', 'runs' => '', 'status' => 'live',
    'ip' => '', 'port' => '', 'on_machine' => '', 'why' => '', 'notes' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $form = [
        'hostname'   => strtolower(post_string('hostname')),
        'tier'       => post_string('tier'),
        'role'       => post_string('role'),
        'runs'       => post_string('runs'),
        'status'     => post_string('status'),
        'ip'         => post_string('ip'),
        'port'       => post_string('port'),
        'on_machine' => post_string('on_machine'),
        'why'        => post_string('why'),
        'notes'      => post_string('notes'),
    ];

    if (!preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $form['hostname'])) {
        $errors[] = 'Hostname must be lowercase letters, numbers and hyphens only, e.g. "app1".';
    }
    if (!in_array($form['tier'], ['machine', 'webapp', 'service'], true)) {
        $errors[] = 'Pick a valid tier.';
    }
    if (!in_array($form['status'], ['live', 'planned'], true)) {
        $errors[] = 'Pick a valid status.';
    }

    if (!$errors) {
        try {
            if ($isEdit) {
                $stmt = $pdo->prepare(
                    'UPDATE hosts SET hostname=?, tier=?, role=?, runs=?, status=?, ip=?, port=?, on_machine=?, why=?, notes=?, updated_by=?
                     WHERE id=?'
                );
                $stmt->execute([
                    $form['hostname'], $form['tier'], $form['role'], $form['runs'], $form['status'],
                    $form['ip'], $form['port'], $form['on_machine'], $form['why'], $form['notes'],
                    $user['id'], $id,
                ]);
                $details = diff_summary($existing, $form, ['hostname', 'tier', 'role', 'runs', 'status', 'ip', 'port', 'on_machine']);
                log_action('update', 'host', $form['hostname'], $details);
                set_flash('success', 'Updated ' . $form['hostname'] . '.');
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO hosts (hostname, tier, role, runs, status, ip, port, on_machine, why, notes, created_by, updated_by)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $form['hostname'], $form['tier'], $form['role'], $form['runs'], $form['status'],
                    $form['ip'], $form['port'], $form['on_machine'], $form['why'], $form['notes'],
                    $user['id'], $user['id'],
                ]);
                log_action('create', 'host', $form['hostname']);
                set_flash('success', 'Added ' . $form['hostname'] . '.');
            }
            redirect('index.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'A hostname called "' . $form['hostname'] . '" already exists.';
            } else {
                throw $e;
            }
        }
    }
}

$machines = $pdo->query("SELECT hostname FROM hosts WHERE tier = 'machine' ORDER BY hostname")->fetchAll(PDO::FETCH_COLUMN);
$suggestions = $isEdit ? [] : unused_suggestions();

$pageTitle = $isEdit ? 'Edit hostname' : 'Add hostname';
$heading = $isEdit ? 'Edit ' . $existing['hostname'] : 'Add a hostname';
$subheading = $isEdit ? 'Change details for this entry.' : 'Fill in a new machine, web app or service.';
$navActive = 'hosts';
require __DIR__ . '/includes/layout_top.php';
?>

<?php foreach ($errors as $e): ?>
  <div class="flash error"><?= esc($e) ?></div>
<?php endforeach; ?>

<?php if (!$isEdit && $suggestions): ?>
<details class="suggest-box">
  <summary>Need a name? Pick from the suggestion list (<?= count($suggestions) ?> unused)</summary>
  <div class="suggest-grid" id="suggestGrid"></div>
</details>
<?php endif; ?>

<div class="form-card">
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field">
        <label for="hostname">Hostname</label>
        <input type="text" id="hostname" name="hostname" value="<?= esc($form['hostname']) ?>" required pattern="[a-z0-9-]{1,63}" placeholder="app1">
        <span class="hint">Becomes <?= esc(get_config()['domain'] ?? 'example.com') ?> subdomain.</span>
      </div>
      <div class="field">
        <label for="tier">Tier</label>
        <select id="tier" name="tier" required>
          <option value="machine" <?= $form['tier'] === 'machine' ? 'selected' : '' ?>>Machine</option>
          <option value="webapp" <?= $form['tier'] === 'webapp' ? 'selected' : '' ?>>Web app</option>
          <option value="service" <?= $form['tier'] === 'service' ? 'selected' : '' ?>>Service</option>
        </select>
      </div>
      <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status" required>
          <option value="live" <?= $form['status'] === 'live' ? 'selected' : '' ?>>Live</option>
          <option value="planned" <?= $form['status'] === 'planned' ? 'selected' : '' ?>>Planned</option>
        </select>
      </div>
      <div class="field">
        <label for="role">Role</label>
        <input type="text" id="role" name="role" value="<?= esc($form['role']) ?>" placeholder="Password manager">
      </div>
      <div class="field">
        <label for="runs">Runs</label>
        <input type="text" id="runs" name="runs" value="<?= esc($form['runs']) ?>" placeholder="Vaultwarden">
      </div>
      <div class="field">
        <label for="on_machine">On machine</label>
        <input type="text" id="on_machine" name="on_machine" value="<?= esc($form['on_machine']) ?>" list="machineList" placeholder="server1">
        <datalist id="machineList">
          <?php foreach ($machines as $m): ?><option value="<?= esc($m) ?>"><?php endforeach; ?>
        </datalist>
      </div>
      <div class="field">
        <label for="ip">Internal IP</label>
        <input type="text" id="ip" name="ip" value="<?= esc($form['ip']) ?>" placeholder="10.0.10.12">
      </div>
      <div class="field">
        <label for="port">Port</label>
        <input type="text" id="port" name="port" value="<?= esc($form['port']) ?>" placeholder="8222">
      </div>
      <div class="field full">
        <label for="why">Why this name</label>
        <input type="text" id="why" name="why" value="<?= esc($form['why']) ?>">
      </div>
      <div class="field full">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes"><?= esc($form['notes']) ?></textarea>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save changes' : 'Add hostname' ?></button>
      <a class="btn" href="index.php">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>

<?php if (!$isEdit && $suggestions): ?>
<script>
const SUGGESTIONS = <?= json_for_script($suggestions) ?>;
const grid = document.getElementById("suggestGrid");
grid.innerHTML = SUGGESTIONS.map((s, i) =>
  '<button type="button" class="suggest-item" data-i="' + i + '">'
  + '<span class="sh">' + s.host + '</span>'
  + (s.role ? '<span class="sr">' + s.role + '</span>' : '<span class="sr">' + s.tier + '</span>')
  + '</button>'
).join("");
grid.addEventListener("click", e => {
  const btn = e.target.closest(".suggest-item");
  if (!btn) return;
  const s = SUGGESTIONS[+btn.dataset.i];
  document.getElementById("hostname").value = s.host;
  document.getElementById("tier").value = s.tier;
  document.getElementById("role").value = s.role || "";
  document.getElementById("runs").value = s.runs || "";
  document.getElementById("why").value = s.why || "";
  document.getElementById("notes").value = s.notes || "";
});
</script>
<?php endif; ?>
