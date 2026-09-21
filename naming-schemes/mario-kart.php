<?php
declare(strict_types=1);

/**
 * Naming scheme: Mario Kart. Characters for machines, tracks and places
 * for web apps, items and power-ups for services. Test/demo content -
 * copy this into app/naming-schemes/ on an install to use it.
 */
return [

    // Machines: characters.
    ['host' => 'mario',        'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'The default choice - reliable, does a bit of everything'],
    ['host' => 'luigi',        'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Always in the shadow of the main box - a fit for your standby or backup host'],
    ['host' => 'peach',        'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'bowser',       'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'The heavyweight - biggest and toughest box in the fleet'],
    ['host' => 'yoshi',        'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'toad',         'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Small and reliable, does the legwork'],
    ['host' => 'wario',        'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'waluigi',      'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'rosalina',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Watches over everything from above - fits an observer or monitoring host'],
    ['host' => 'daisy',        'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'koopa',        'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'dry-bones',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Keeps coming back no matter what you throw at it'],
    ['host' => 'kamek',        'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'diddy-kong',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'donkey-kong',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Big and strong - a fit for your heaviest compute box'],

    // Web apps: tracks and places.
    ['host' => 'rainbow-road',      'tier' => 'webapp', 'role' => 'Dashboard and start page', 'runs' => '', 'why' => 'The track everyone recognises - a fit for the front door of the network'],
    ['host' => 'peach-circuit',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'moo-moo-meadows',   'tier' => 'webapp', 'role' => 'Home automation', 'runs' => '', 'why' => 'A farm - fitting for the thing watching over the house'],
    ['host' => 'bowsers-castle',    'tier' => 'webapp', 'role' => 'Firewall dashboard', 'runs' => '', 'why' => 'The most heavily guarded place in the kingdom'],
    ['host' => 'toad-harbour',      'tier' => 'webapp', 'role' => 'File sync and storage', 'runs' => '', 'why' => 'A harbour is where things come and go'],
    ['host' => 'royal-raceway',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'dk-jungle',         'tier' => 'webapp', 'role' => 'Media server', 'runs' => '', 'why' => 'Where all the fun happens'],
    ['host' => 'shy-guy-bazaar',    'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'A bazaar is where you browse and trade'],
    ['host' => 'coconut-mall',      'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'wario-stadium',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'luigis-mansion',    'tier' => 'webapp', 'role' => 'Password manager', 'runs' => 'Vaultwarden', 'why' => "Full of hidden things you need a light to find"],

    // Services: items and power-ups.
    ['host' => 'star',           'tier' => 'service', 'role' => 'Alerting and notifications', 'runs' => '', 'why' => 'Makes you invincible for a moment - the alert that saves you'],
    ['host' => 'mushroom',       'tier' => 'service', 'role' => 'Backups', 'runs' => '', 'why' => 'An extra life, just in case'],
    ['host' => 'shell',          'tier' => 'service', 'role' => 'Reverse proxy', 'runs' => '', 'why' => 'Deflects what comes at you before it lands'],
    ['host' => 'banana',         'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'boo',            'tier' => 'service', 'role' => 'Intrusion detection', 'runs' => '', 'why' => 'Sneaks up when you are not looking'],
    ['host' => 'bullet-bill',    'tier' => 'service', 'role' => 'Scheduled jobs', 'runs' => '', 'why' => 'Moves fast in a straight line and does not stop for anything'],
    ['host' => 'lightning',      'tier' => 'service', 'role' => 'Rate limiting', 'runs' => '', 'why' => 'Slows everyone else down'],
    ['host' => 'blue-shell',     'tier' => 'service', 'role' => 'Load balancer', 'runs' => '', 'why' => 'Always finds whoever is out in front'],
    ['host' => 'feather',        'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'golden-mushroom','tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'ice-flower',     'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
];
