<?php
declare(strict_types=1);

/**
 * Naming scheme: space and satellites. Planets and moons for machines,
 * missions and observatories for web apps, satellites and probes for
 * services. Test/demo content - copy this into app/naming-schemes/ on an
 * install to use it.
 *
 * Note: "apollo" and "artemis" also appear in
 * greek-and-norse-mythology.php, on purpose - a deliberate cross-file
 * duplicate to demonstrate the alphabetical-filename tiebreak. That file
 * sorts before this one, so its entries win both times; the two rows
 * below are here to test that they get skipped, not to be picked.
 */
return [

    // Machines: planets and moons.
    ['host' => 'mercury', 'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Closest to the sun, fast and small'],
    ['host' => 'venus',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'mars',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'The next frontier - fits a box you are still setting up'],
    ['host' => 'jupiter', 'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Biggest planet in the solar system - a fit for your heaviest box'],
    ['host' => 'saturn',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'uranus',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'neptune', 'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'pluto',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => '', 'notes' => 'Technically reclassified as a dwarf planet in 2006. Use it anyway if you liked it first.'],
    ['host' => 'europa',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => "One of Jupiter's moons, thought to hide an ocean underneath - fits a storage box"],
    ['host' => 'titan',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => "Saturn's largest moon and a thick atmosphere - a heavy box"],
    ['host' => 'ceres',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'io',      'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'luna',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'phobos',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'deimos',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],

    // Web apps: missions and observatories.
    ['host' => 'voyager',      'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'Still sending data back decades later - the box that just keeps going'],
    ['host' => 'hubble',       'tier' => 'webapp', 'role' => 'Monitoring dashboards', 'runs' => '', 'why' => 'Looks out and reports what it sees'],
    ['host' => 'kepler',       'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'Found thousands of new worlds - a fit for a discovery or search tool'],
    ['host' => 'apollo',       'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'NASA programme that put humans on the Moon', 'notes' => 'Duplicate of greek-and-norse-mythology.php on purpose - that file wins.'],
    ['host' => 'artemis',      'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'NASA programme returning humans to the Moon', 'notes' => 'Duplicate of greek-and-norse-mythology.php on purpose - that file wins.'],
    ['host' => 'chandra',      'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'X-ray observatory'],
    ['host' => 'spitzer',      'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'james-webb',   'tier' => 'webapp', 'role' => 'Photo library', 'runs' => '', 'why' => 'Sees deeper and clearer than anything before it'],

    // Services: satellites and probes.
    ['host' => 'starlink',     'tier' => 'service', 'role' => 'Mesh overlay network', 'runs' => '', 'why' => 'A constellation of small nodes covering everything'],
    ['host' => 'sputnik',      'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'The first artificial satellite'],
    ['host' => 'cassini',      'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'Studied Saturn for over a decade before diving in'],
    ['host' => 'galileo',      'tier' => 'service', 'role' => 'DNS and ad filtering', 'runs' => '', 'why' => "Europe's satellite navigation system - helps everything find its way"],
    ['host' => 'iss',          'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'Always orbiting, always online'],
    ['host' => 'gps',          'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'Helps everything find where it needs to go'],
    ['host' => 'new-horizons', 'tier' => 'service', 'role' => '', 'runs' => '', 'why' => 'Took nine years to reach Pluto and kept going'],
];
