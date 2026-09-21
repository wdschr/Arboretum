<?php
/**
 * Copy this file to config.php and fill in your real values.
 *
 * config.php must NOT be uploaded into public_html. Put it in a folder
 * called private_html, sitting next to public_html (see SETUP.md for the
 * exact path - it depends on how your app is deployed), e.g.
 *
 *   /home/sites/<your-site-id>/private_html/config.php
 *   /home/sites/<your-site-id>/public_html/           <- app files go in here
 *
 * That way no URL can ever serve this file, even by accident. If you use
 * install.php instead of copying this file by hand, it works this path
 * out and creates the folder for you.
 */
return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'your_database_name',
        'user'    => 'your_database_user',
        'pass'    => 'your_database_password',
        'charset' => 'utf8mb4',
    ],

    // The app's name, shown in the page title and header.
    'site_name' => 'Arboretum',

    // The domain hostnames get appended to, e.g. "app1" becomes
    // "app1.example.com". Live hosts link to https://<that>/.
    'domain' => 'example.com',

    // A one-time secret only you know. setup.php asks for this before it
    // will create the first admin account, so a stranger who finds the
    // URL before you run it can't grab admin for themselves. Make this a
    // long random string, e.g. from https://www.random.org/strings/ or
    // `openssl rand -hex 20`. Once setup is done, delete setup.php from
    // the server entirely - you won't need it again.
    'setup_key' => 'change-this-to-something-long-and-random',

    // Change this to something random and unique to your site. It is
    // mixed into the session cookie name only, not used for anything
    // cryptographic.
    'session_name' => 'arboretum_session',
];
