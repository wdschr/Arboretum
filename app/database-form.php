<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/audit.php';

$user = require_role(['admin', 'editor']);
$pdo = db();

$id = get_int('id', 0);
$isEdit = $id > 0;
$existing = null;

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM databases_directory WHERE id = ?');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) {
        set_flash('error', 'That database no longer exists.');
        redirect('databases.php');
    }
}

$errors = [];
$form = $existing ?? [
    'name' => '', 'engine_name' => '', 'host_machine' => '', 'port' => '',
    'purpose' => '', 'status' => 'live', 'notes' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $form = [
        'name'         => post_string('name'),
        'engine_name'  => post_string('engine_name'),
        'host_machine' => post_string('host_machine'),
        'port'         => post_string('port'),
        'purpose'      => post_string('purpose'),
        'status'       => post_string('status'),
        'notes'        => post_string('notes'),
    ];

    if ($form['name'] === '' || mb_strlen($form['name']) > 100) {
        $errors[] = 'Give the database a name.';
    }
    if (!in_array($form['status'], ['live', 'planned'], true)) {
        $errors[] = 'Pick a valid status.';
    }
    if (preg_match('/password|secret|token|credential/i', $form['notes'] . ' ' . $form['purpose'])) {
        $errors[] = 'Notes mention a password/secret/token/credential. This directory is not for storing credentials - remove that before saving.';
    }

    if (!$errors) {
        try {
            if ($isEdit) {
                $stmt = $pdo->prepare(
                    'UPDATE databases_directory SET name=?, engine_name=?, host_machine=?, port=?, purpose=?, status=?, notes=?, updated_by=?
                     WHERE id=?'
                );
                $stmt->execute([
                    $form['name'], $form['engine_name'], $form['host_machine'], $form['port'],
                    $form['purpose'], $form['status'], $form['notes'], $user['id'], $id,
                ]);
                $details = diff_summary($existing, $form, ['name', 'engine_name', 'host_machine', 'port', 'purpose', 'status']);
                log_action('update', 'database', $form['name'], $details);
                set_flash('success', 'Updated ' . $form['name'] . '.');
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO databases_directory (name, engine_name, host_machine, port, purpose, status, notes, created_by, updated_by)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $form['name'], $form['engine_name'], $form['host_machine'], $form['port'],
                    $form['purpose'], $form['status'], $form['notes'], $user['id'], $user['id'],
                ]);
                log_action('create', 'database', $form['name']);
                set_flash('success', 'Added ' . $form['name'] . '.');
            }
            redirect('databases.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'A database called "' . $form['name'] . '" already exists.';
            } else {
                throw $e;
            }
        }
    }
}

$machines = $pdo->query("SELECT hostname FROM hosts WHERE tier = 'machine' ORDER BY hostname")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = $isEdit ? 'Edit database' : 'Add database';
$heading = $isEdit ? 'Edit ' . $existing['name'] : 'Add a database';
$subheading = $isEdit ? 'Change details for this entry.' : 'Fill in a new database. No credentials, just what it is and where it lives.';
$navActive = 'databases';
require __DIR__ . '/includes/layout_top.php';
?>

<?php foreach ($errors as $e): ?>
  <div class="flash error"><?= esc($e) ?></div>
<?php endforeach; ?>

<div class="form-card">
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= esc($form['name']) ?>" required placeholder="vaultwarden_prod">
      </div>
      <div class="field">
        <label for="engine_name">Engine</label>
        <input type="text" id="engine_name" name="engine_name" value="<?= esc($form['engine_name']) ?>" list="engineList" placeholder="MySQL">
        <datalist id="engineList">
          <option value="MySQL"><option value="MariaDB"><option value="PostgreSQL">
          <option value="SQLite"><option value="Redis"><option value="MongoDB">
        </datalist>
      </div>
      <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status" required>
          <option value="live" <?= $form['status'] === 'live' ? 'selected' : '' ?>>Live</option>
          <option value="planned" <?= $form['status'] === 'planned' ? 'selected' : '' ?>>Planned</option>
        </select>
      </div>
      <div class="field">
        <label for="host_machine">Host machine</label>
        <input type="text" id="host_machine" name="host_machine" value="<?= esc($form['host_machine']) ?>" list="machineList" placeholder="server1">
        <datalist id="machineList">
          <?php foreach ($machines as $m): ?><option value="<?= esc($m) ?>"><?php endforeach; ?>
        </datalist>
      </div>
      <div class="field">
        <label for="port">Port</label>
        <input type="text" id="port" name="port" value="<?= esc($form['port']) ?>" placeholder="3306">
      </div>
      <div class="field full">
        <label for="purpose">Purpose / used by</label>
        <input type="text" id="purpose" name="purpose" value="<?= esc($form['purpose']) ?>" placeholder="Backend for app1.example.com">
      </div>
      <div class="field full">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes"><?= esc($form['notes']) ?></textarea>
        <span class="hint">No passwords or credentials here - this directory only tracks what exists, not how to get into it.</span>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save changes' : 'Add database' ?></button>
      <a class="btn" href="databases.php">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
