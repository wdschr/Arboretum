<?php
declare(strict_types=1);

/**
 * Naming scheme: Greek and Norse mythology. Major gods for machines,
 * realms and places for web apps, creatures and concepts for services.
 * Test/demo content - copy this into app/naming-schemes/ on an install
 * to use it.
 *
 * Note: "apollo" and "artemis" also appear in the space-and-satellites.php
 * naming scheme (as NASA mission names), on purpose - a deliberate
 * cross-file duplicate to demonstrate that when two naming schemes are
 * both active, the alphabetically earliest filename wins for a shared
 * hostname. "greek-and-norse-mythology.php" sorts before
 * "space-and-satellites.php", so this file's entries win both times.
 */
return [

    // Machines: gods.
    ['host' => 'zeus',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'King of the gods - the box everything else answers to'],
    ['host' => 'odin',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'All-father, watches over everything - fits your primary host'],
    ['host' => 'hera',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'thor',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Raw power - the heavy-lifting machine'],
    ['host' => 'poseidon', 'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Rules the depths - fitting for storage-heavy hardware'],
    ['host' => 'hades',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Rules the underworld - the box tucked away out of sight'],
    ['host' => 'athena',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Goddess of wisdom and strategy'],
    ['host' => 'freya',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'baldr',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'apollo',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'God of light - fits the box that keeps the lights on', 'notes' => 'Also used in space-and-satellites.php for the NASA programme - this file wins the clash.'],
    ['host' => 'artemis',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => '', 'notes' => 'Also used in space-and-satellites.php for the NASA programme - this file wins the clash.'],
    ['host' => 'ares',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'tyr',      'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'God of law and single combat'],
    ['host' => 'frigg',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],

    // Web apps: realms and places.
    ['host' => 'olympus',    'tier' => 'webapp', 'role' => 'Dashboard and start page', 'runs' => '', 'why' => 'Home of the gods, the top of everything'],
    ['host' => 'asgard',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'Realm of the Aesir gods'],
    ['host' => 'valhalla',   'tier' => 'webapp', 'role' => 'Media server', 'runs' => '', 'why' => 'Where warriors feast and celebrate forever'],
    ['host' => 'elysium',    'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'The paradise afterlife'],
    ['host' => 'midgard',    'tier' => 'webapp', 'role' => 'Home automation', 'runs' => '', 'why' => 'The realm of humans, the middle world'],
    ['host' => 'yggdrasil',  'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'The world tree connecting all nine realms - fits the thing tying your network together'],
    ['host' => 'heimdall',   'tier' => 'webapp', 'role' => 'Dashboard and start page', 'runs' => 'Heimdall', 'why' => 'Watches the rainbow bridge and sees everything - also a real self-hosted dashboard app'],
    ['host' => 'delphi',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'Where oracles gave their answers'],

    // Services: creatures and concepts.
    ['host' => 'cerberus', 'tier' => 'service', 'role' => 'Firewall and intrusion blocking', 'runs' => '', 'why' => 'Three-headed guard dog at the gates of the underworld'],
    ['host' => 'fenrir',   'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'The great wolf, bound until the end'],
    ['host' => 'kraken',   'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'Lurks in the depths'],
    ['host' => 'pegasus',  'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'Fast and free'],
    ['host' => 'minotaur', 'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'Guards the labyrinth'],
    ['host' => 'sleipnir', 'tier' => 'service', 'role' => '', 'runs' => '', 'why' => "Odin's eight-legged horse - the fastest thing around"],
    ['host' => 'hydra',    'tier' => 'service', 'role' => 'Replication', 'runs' => '', 'why' => 'Cut off one head, two more take its place'],
    ['host' => 'loki',     'tier' => 'service', 'role' => 'Scheduled jobs', 'runs' => '', 'why' => 'The trickster, always up to something behind the scenes'],
    ['host' => 'valkyrie', 'tier' => 'service', 'role' => 'Alerting and notifications', 'runs' => '', 'why' => 'Chooses who falls, carries the message'],
    ['host' => 'charon',   'tier' => 'service', 'role' => 'Backups', 'runs' => '', 'why' => 'Ferries things across to the other side'],
];
