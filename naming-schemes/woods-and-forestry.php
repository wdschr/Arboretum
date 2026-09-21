<?php
declare(strict_types=1);

/**
 * Naming scheme: a word list Arboretum's "add hostname" form can suggest
 * from. This one uses trees for machines, places in a wood for web apps,
 * and woodland creatures and processes for services - nothing to do with
 * the app's own visual appearance, which this can't change.
 *
 * This file lives outside the deployed app on purpose (this
 * naming-schemes/ folder at the repo root) - it's not loaded by Arboretum
 * from here. To use it, copy it into the app/naming-schemes/ folder on
 * your own install (create that folder if it doesn't exist) and it's
 * picked up automatically the next time someone opens the "add hostname"
 * form. Delete it from there to turn its suggestions off again.
 *
 * You can have several naming-scheme files active there at once -
 * everything directly inside app/naming-schemes/ gets loaded and merged
 * together (first match wins, alphabetically by filename, if two suggest
 * the same hostname). Copy this file under a new name to add another one
 * alongside it, or write your own from scratch: it just needs to return
 * an array of entries shaped like the ones below. `host` and `tier`
 * (machine/webapp/service) are required; `role`, `runs`, `why` and
 * `notes` are optional strings used only to pre-fill the form.
 *
 * None of this is live data. It only pre-fills the add-hostname form when
 * someone clicks a suggestion - nothing is written to the database until
 * they save it.
 */
return [

    // Machines: trees.
    ['host' => 'oak',       'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'The heaviest and most stable box, the one everything else leans on'],
    ['host' => 'ash',       'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'birch',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'rowan',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Rowan is the protection tree in British folklore, a fit for whichever box holds identity'],
    ['host' => 'hazel',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'alder',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Alder hardens in water, a fit for the box you never turn off'],
    ['host' => 'willow',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'holly',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'larch',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'aspen',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'maple',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'cedar',     'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'juniper',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => '', 'notes' => 'Juniper is a network vendor. Skip if you run their kit.'],
    ['host' => 'spruce',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'poplar',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'linden',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'walnut',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'chestnut',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'hornbeam',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => '', 'notes' => 'Rhymes with whitebeam. Use one of the two.'],
    ['host' => 'whitebeam', 'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => '', 'notes' => 'Rhymes with hornbeam. Use one of the two.'],
    ['host' => 'hawthorn',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'medlar',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'damson',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'cypress',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'redwood',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Biggest thing in the wood, a fit for your largest host'],
    ['host' => 'sequoia',   'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => '', 'notes' => 'Also a macOS release name. Confusing in Jamf-adjacent chat.'],
    ['host' => 'ironwood',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Densest timber going, a fit for the box built to take a beating'],
    ['host' => 'tamarack',  'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'banyan',    'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => 'Grows outward into many trunks, a fit for a cluster node'],
    ['host' => 'teak',      'tier' => 'machine', 'role' => '', 'runs' => '', 'why' => ''],

    // Web apps: places in the wood.
    ['host' => 'trailhead', 'tier' => 'webapp', 'role' => 'Dashboard and start page', 'runs' => 'Homepage or Heimdall', 'why' => 'Where all the paths meet'],
    ['host' => 'arboretum', 'tier' => 'webapp', 'role' => 'This hostname directory', 'runs' => '', 'why' => 'A catalogued collection of trees, which is what this page is'],
    ['host' => 'lodge',     'tier' => 'webapp', 'role' => 'Household wiki', 'runs' => 'BookStack or Wiki.js', 'why' => 'The building everyone gathers in'],
    ['host' => 'hide',      'tier' => 'webapp', 'role' => 'Monitoring dashboards', 'runs' => 'Grafana', 'why' => 'A wildlife hide is where you sit and watch'],
    ['host' => 'lookout',   'tier' => 'webapp', 'role' => 'Uptime and status', 'runs' => 'Uptime Kuma', 'why' => 'Sees trouble coming'],
    ['host' => 'dapple',    'tier' => 'webapp', 'role' => 'Photo library', 'runs' => 'Immich or PhotoPrism', 'why' => 'Dappled light through leaves'],
    ['host' => 'ember',     'tier' => 'webapp', 'role' => 'Media server', 'runs' => 'Jellyfin or Plex', 'why' => 'Stories round the fire'],
    ['host' => 'rookery',   'tier' => 'webapp', 'role' => 'Household chat', 'runs' => 'Matrix or Rocket.Chat', 'why' => 'A noisy gathering in the treetops'],
    ['host' => 'folio',     'tier' => 'webapp', 'role' => 'Document scanning and OCR', 'runs' => 'Paperless-ngx', 'why' => 'A folio is a leaf of paper'],
    ['host' => 'tally',     'tier' => 'webapp', 'role' => 'Notes', 'runs' => 'Joplin or Memos', 'why' => 'From notched wooden tally sticks'],
    ['host' => 'windfall',  'tier' => 'webapp', 'role' => 'RSS and feeds', 'runs' => 'FreshRSS or Miniflux', 'why' => 'Arrives without being fetched'],
    ['host' => 'forage',    'tier' => 'webapp', 'role' => 'Downloads and grabbers', 'runs' => 'Sonarr, Radarr, qBittorrent', 'why' => 'Gathering from the woods'],
    ['host' => 'bramble',   'tier' => 'webapp', 'role' => 'Recipes and meal planning', 'runs' => 'Mealie or Tandoor', 'why' => 'Brambles give the berries'],
    ['host' => 'drey',      'tier' => 'webapp', 'role' => 'Password manager', 'runs' => 'Vaultwarden', 'why' => "A drey is the squirrel's store"],
    ['host' => 'burrow',    'tier' => 'webapp', 'role' => 'File sync and storage', 'runs' => 'Nextcloud', 'why' => 'Where things get kept underground'],
    ['host' => 'bough',     'tier' => 'webapp', 'role' => 'Git and code', 'runs' => 'Forgejo or Gitea', 'why' => 'Branches'],
    ['host' => 'waymark',   'tier' => 'webapp', 'role' => 'Bookmarks', 'runs' => 'Linkding or Karakeep', 'why' => 'Trail markers'],
    ['host' => 'solstice',  'tier' => 'webapp', 'role' => 'Calendar and contacts', 'runs' => 'Radicale or Baikal', 'why' => 'Turns with the seasons'],
    ['host' => 'hearth',    'tier' => 'webapp', 'role' => 'Home automation', 'runs' => 'Home Assistant', 'why' => 'The centre of the house'],
    ['host' => 'orchard',   'tier' => 'webapp', 'role' => 'Ebooks and audiobooks', 'runs' => 'Calibre-Web or Audiobookshelf', 'why' => 'Rows of fruit, picked when wanted'],
    ['host' => 'glade',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'grove',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'clearing',  'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'coppice',   'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'spinney',   'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'combe',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'A combe is a short wooded valley'],
    ['host' => 'holt',      'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => "Old English for a small wood, and an otter's home"],
    ['host' => 'shaw',      'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'A shaw is a strip of woodland along a field edge'],
    ['host' => 'hurst',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'A hurst is a wooded hill, as in Chislehurst'],
    ['host' => 'weald',     'tier' => 'webapp', 'role' => '', 'runs' => '', 'why' => 'Old word for wooded country'],

    // Services: creatures and processes.
    ['host' => 'stile',       'tier' => 'service', 'role' => 'Reverse proxy', 'runs' => 'Caddy, Traefik or NPM', 'why' => 'The controlled crossing into everything else'],
    ['host' => 'warden',      'tier' => 'service', 'role' => 'SSO and identity', 'runs' => 'Authentik, Authelia or Keycloak', 'why' => 'Checks who gets in'],
    ['host' => 'badger',      'tier' => 'service', 'role' => 'VPN and remote access', 'runs' => 'WireGuard', 'why' => 'Digs the tunnel'],
    ['host' => 'taproot',     'tier' => 'service', 'role' => 'DNS and ad filtering', 'runs' => 'Pi-hole or AdGuard Home', 'why' => 'Roots find what everything else asks for'],
    ['host' => 'mycelium',    'tier' => 'service', 'role' => 'Mesh overlay network', 'runs' => 'Tailscale or Headscale', 'why' => 'The network under the whole wood, invisible from above'],
    ['host' => 'ford',        'tier' => 'service', 'role' => 'HomeKit bridge', 'runs' => 'Homebridge or Matterbridge', 'why' => 'A ford is the crossing between two sides'],
    ['host' => 'nettle',      'tier' => 'service', 'role' => 'Firewall and intrusion blocking', 'runs' => 'OPNsense or CrowdSec', 'why' => 'Stings anything pushing through'],
    ['host' => 'thistle',     'tier' => 'service', 'role' => 'Brute-force blocking', 'runs' => 'fail2ban', 'why' => 'Second line of stings'],
    ['host' => 'hedgehog',    'tier' => 'service', 'role' => 'Vulnerability scanning', 'runs' => 'Trivy or Grype', 'why' => 'Rolls up and checks the spikes'],
    ['host' => 'kestrel',     'tier' => 'service', 'role' => 'Metrics collection', 'runs' => 'Prometheus', 'why' => 'Hovers and watches'],
    ['host' => 'lichen',      'tier' => 'service', 'role' => 'Log aggregation', 'runs' => 'Loki', 'why' => 'Grows slowly over everything and records the air'],
    ['host' => 'heartwood',   'tier' => 'service', 'role' => 'Backups', 'runs' => 'Restic or Borg', 'why' => 'The old dense core holding the record'],
    ['host' => 'amber',       'tier' => 'service', 'role' => 'Long-term archive', 'runs' => '', 'why' => 'Resin that keeps things exactly as they were'],
    ['host' => 'jay',         'tier' => 'service', 'role' => 'Cache', 'runs' => 'Redis or Valkey', 'why' => 'Jays bury acorns and remember where'],
    ['host' => 'otter',       'tier' => 'service', 'role' => 'File sync daemon', 'runs' => 'Syncthing', 'why' => 'Moves between banks without fuss'],
    ['host' => 'woodpecker',  'tier' => 'service', 'role' => 'CI runner', 'runs' => 'Woodpecker CI', 'why' => 'Hammers away until the job is done'],
    ['host' => 'treecreeper', 'tier' => 'service', 'role' => 'Search indexer', 'runs' => '', 'why' => 'Works up the trunk covering every inch'],
    ['host' => 'tawny',       'tier' => 'service', 'role' => 'Scheduled jobs', 'runs' => 'cron or Ofelia', 'why' => 'Tawny owl, works while the house sleeps'],
    ['host' => 'pipistrelle', 'tier' => 'service', 'role' => 'Overnight maintenance', 'runs' => '', 'why' => 'Out only after dark'],
    ['host' => 'magpie',      'tier' => 'service', 'role' => 'Metadata scraper', 'runs' => '', 'why' => 'Collects anything shiny'],
    ['host' => 'xylem',       'tier' => 'service', 'role' => 'Data pipeline, inbound', 'runs' => '', 'why' => 'Carries water up the tree'],
    ['host' => 'phloem',      'tier' => 'service', 'role' => 'Data pipeline, outbound', 'runs' => '', 'why' => 'Carries sugars back down'],
    ['host' => 'sap',         'tier' => 'service', 'role' => 'Message broker and queue', 'runs' => 'RabbitMQ or NATS', 'why' => 'What flows between everything else', 'notes' => 'Also a large software vendor. Harmless at home, awkward at work.'],
    ['host' => 'loam',        'tier' => 'service', 'role' => 'Storage pool', 'runs' => 'ZFS or TrueNAS', 'why' => 'The layer everything grows out of'],
    ['host' => 'spore',       'tier' => 'service', 'role' => 'Replication', 'runs' => '', 'why' => 'Copies itself somewhere else'],
    ['host' => 'pollen',      'tier' => 'service', 'role' => 'Update distribution', 'runs' => '', 'why' => 'Spreads without being asked'],
    ['host' => 'moss',        'tier' => 'service', 'role' => 'Static file store', 'runs' => 'MinIO or Garage', 'why' => 'Sits still and takes up no effort'],
    ['host' => 'bracken',     'tier' => 'service', 'role' => 'Container runtime', 'runs' => 'Docker or Podman', 'why' => 'Spreads fast and fills the ground'],
    ['host' => 'heron',       'tier' => 'service', 'role' => 'Container image updates', 'runs' => 'Watchtower or Renovate', 'why' => 'Stands still for hours, then strikes'],
    ['host' => 'raven',       'tier' => 'service', 'role' => 'Alerting and notifications', 'runs' => 'Gotify or ntfy', 'why' => 'Carries the message'],
    ['host' => 'dormouse',    'tier' => 'service', 'role' => 'Suspend and wake', 'runs' => 'Wake-on-LAN', 'why' => 'Sleeps most of the year'],
    ['host' => 'cambium',     'tier' => 'service', 'role' => 'Build server', 'runs' => '', 'why' => 'The thin layer where the tree actually grows'],
    ['host' => 'nursery',     'tier' => 'service', 'role' => 'Staging and test', 'runs' => '', 'why' => 'Where saplings go before planting out'],
    ['host' => 'deadwood',    'tier' => 'service', 'role' => 'Decommissioned, held before deletion', 'runs' => '', 'why' => 'Still standing, no longer growing'],
    ['host' => 'wren',        'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'finch',       'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'jackdaw',     'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'stoat',       'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'fern',        'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
    ['host' => 'briar',       'tier' => 'service', 'role' => '', 'runs' => '', 'why' => ''],
];
