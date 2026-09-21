<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

/**
 * Records one line in the audit log. entity_type is a short tag such as
 * 'host', 'database' or 'user'. entity_label is the human-readable name
 * (hostname, database name, username) at the time of the action.
 */
function log_action(string $action, string $entityType, string $entityLabel, string $details = ''): void
{
    $user = current_user();
    $stmt = db()->prepare(
        'INSERT INTO audit_log (user_id, username, action, entity_type, entity_label, details)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $user['id'] ?? null,
        $user['username'] ?? 'system',
        $action,
        $entityType,
        $entityLabel,
        $details !== '' ? $details : null,
    ]);
}

/** Builds a short "field: old -> new" summary for an update, skipping unchanged fields. */
function diff_summary(array $before, array $after, array $fields): string
{
    $parts = [];
    foreach ($fields as $field) {
        $old = (string)($before[$field] ?? '');
        $new = (string)($after[$field] ?? '');
        if ($old !== $new) {
            $parts[] = $field . ': "' . $old . '" -> "' . $new . '"';
        }
    }
    return implode('; ', $parts);
}

function recent_audit_log(int $limit = 200): array
{
    $limit = max(1, min(1000, $limit));
    $stmt = db()->query('SELECT * FROM audit_log ORDER BY created_at DESC, id DESC LIMIT ' . $limit);
    return $stmt->fetchAll();
}
