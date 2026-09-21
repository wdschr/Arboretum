<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Optional naming suggestions for the "add a hostname" form. Core ships
 * with none at all, not even the app/naming-schemes/ folder - this is a
 * pure extension point for naming-scheme packs distributed separately
 * (see the naming-schemes/ folder at the repo root for a ready-made one).
 *
 * To use one: create an app/naming-schemes/ folder (it doesn't exist by
 * default) and drop a naming-scheme file into it - one that returns an
 * array of entries, each with a `host` and a `tier`
 * (machine/webapp/service) required, plus optional `role`, `runs`, `why`
 * and `notes` strings used only to pre-fill the form. Its entries appear
 * automatically; delete the file (or the whole folder) and the picker
 * stops appearing.
 *
 * Any number of naming-scheme files can be active at once, processed in
 * alphabetical order by filename - if two suggest the same hostname, the
 * alphabetically earliest file wins and the later duplicate is skipped.
 *
 * These are just word lists for filling in the hostname field - nothing
 * to do with the app's visual appearance, which doesn't change.
 *
 * None of this is ever live data. Picking a suggestion only pre-fills
 * the add-hostname form - nothing is written until it's saved.
 */
function suggested_hosts(): array
{
    $dir = __DIR__ . '/../naming-schemes';
    if (!is_dir($dir)) {
        return [];
    }

    $files = glob($dir . '/*.php') ?: [];
    sort($files); // deterministic order: alphabetical by filename, not filesystem-dependent

    $suggestions = [];
    $seen = [];
    foreach ($files as $file) {
        $entries = require $file;
        if (!is_array($entries)) {
            continue;
        }

        foreach ($entries as $entry) {
            $host = strtolower((string)($entry['host'] ?? ''));
            if ($host === '' || isset($seen[$host])) {
                continue;
            }
            $seen[$host] = true;
            $suggestions[] = $entry;
        }
    }

    return $suggestions;
}

/** Suggested names that aren't already rows in the hosts table, grouped by tier. */
function unused_suggestions(): array
{
    $hosts = suggested_hosts();
    if (!$hosts) {
        return [];
    }

    $stmt = db()->query('SELECT hostname FROM hosts');
    $taken = array_flip(array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN)));

    return array_values(array_filter(
        $hosts,
        fn($s) => !isset($taken[strtolower($s['host'])])
    ));
}
