# Arboretum, setup

Arboretum is a small self-hosted web app: a login page, three roles
(admin, editor, viewer), a hostname directory, a separate database
directory, and an audit log of who changed what. It's plain PHP and
MySQL/MariaDB - no framework, no Composer dependencies, no build step -
so it runs on ordinary shared hosting as-is.

Nothing is pre-filled. The directory starts completely empty and there's
no built-in naming scheme - see [Optional: hostname suggestions](#optional-hostname-suggestions)
below if you want one.

## What you need

- Shared (or any) hosting with PHP 8.1+ and one MySQL/MariaDB database.
  Most hosts expose PHP version selection and MySQL database creation
  through a cPanel-style control panel - the steps below use that as the
  general case.
- Access to that control panel, and phpMyAdmin (or equivalent) for the
  manual-install fallback.
- FTP/SFTP access or a File Manager, to upload files.

## 1. Create the database

In your host's control panel: find the MySQL Databases section -> create
a new database (e.g. `arboretum`) -> create a new database user with a
strong, randomly generated password -> add that user to the database with
**All Privileges**.

Note the three values it gives you: the full database name, the full
username, and the password. Many shared hosts prefix both with your
account name, e.g. `abc12_arboretum`.

## 2. Upload the app

Everything inside this repository's `app/` folder goes into your site's
web root (commonly called `public_html`), or a subfolder of it if you'd
rather keep it off the bare domain. Upload the *contents* of `app/`, not
the `app` folder itself, so `index.php` sits directly inside the web root
(or your chosen subfolder).

Do **not** upload `db/schema.sql` or `SETUP.md` as part of that folder's
normal contents - they aren't needed at runtime. (The `.htaccess` included
blocks direct web requests for `.sql`/`.md` files as a backstop, but
there's no reason to upload them at all.)

## 3. Run the installer

Visit `https://your-site/install.php` (or `https://your-site/subfolder/install.php`
if you used a subfolder). It's a small GUI installer, the same idea as the
one Roundcube webmail ships with: it checks your PHP version and
extensions, asks for your database host/name/username/password plus your
site name and domain, tests the connection, creates the four tables
(`users`, `hosts`, `databases_directory`, `audit_log`), and writes
`config.php` to the right place for you.

It writes `config.php` into a folder called **`private_html`**, sitting
next to `public_html` rather than inside it, so no URL can ever reach it.
The installer creates this folder itself if it doesn't exist yet. Where
exactly it ends up mirrors where the app itself lives:

**App at the root of your site** (`public_html/index.php`):

    /home/sites/example.com/private_html/config.php
    /home/sites/example.com/public_html/                  <- app files go here

**App on a subdomain that your host nests inside your main `public_html`**
- some hosts do this (Heart Internet's Extend panel is one example),
e.g. `arboretum.example.com` served from `public_html/arboretum/`. The
installer finds the nearest folder above it that's literally called
`public_html`, and mirrors the same subpath under `private_html` instead:

    /home/sites/example.com/private_html/arboretum/config.php
    /home/sites/example.com/public_html/arboretum/        <- app files go here

`private_html` is just a clear, predictable name - like `public_html`
itself, it's not doing any of the actual security work. What keeps
`config.php` safe is that it's never assigned as a document root for any
domain or subdomain, so no URL can ever point at it, whatever it's called.
The point of putting it in a named, structured folder rather than loose in
the account's home directory is purely so it doesn't look like a stray
file waiting to be tidied away next time you're poking around in FTP.

PHP usually has permission to create `private_html` and write straight
into it, so the installer finishes in one step. If your account doesn't
allow it, the installer shows you the finished file's contents instead -
copy them into a new file called `config.php`, create the `private_html`
folder yourself if needed, and upload it to the path the installer shows
you.

The installer also generates a random **setup key** and shows it to you
once - copy it, you need it on the very next page.

**Delete `install.php` from the server as soon as it's done.** It has no
password protecting it (there's no config yet for it to check one
against), so there's no reason to leave it reachable. It refuses to run a
second time once `config.php` exists, but deleting it removes the file
entirely rather than relying on that check.

If you'd rather do this by hand - or the installer can't reach your
database for some reason - copy `app/config.example.php` to `config.php`
yourself, fill in the values, upload it to the path the installer would
have used (see above), and import `db/schema.sql` via phpMyAdmin's
**Import** tab instead.

If you don't already know your site's absolute path, drop a one-line file
called `whereami.php` into `public_html` containing `<?php echo __DIR__;`,
load it in a browser, note the path it prints, then delete the file.

## 4. Create the first admin account

Visit `https://your-site/setup.php` (or `https://your-site/subfolder/setup.php`
if you used a subfolder). Enter the `setup_key` from `config.php`, pick a
username and password, and submit. This creates exactly one admin account.

**Then delete `setup.php` from the server.** It refuses to run a second
time once any account exists, but there's no reason to leave it reachable.

## 5. Log in and add your first users

Log in with the account you just created, then go to **Users** (top
right) to add everyone else who needs access. Each new account gets a
randomly generated temporary password shown to you once on screen - copy
it and pass it on before you navigate away, because it isn't stored
anywhere retrievable and won't be shown again. Whoever receives it is
forced to set their own password the first time they log in.

Roles:

- **Admin** - everything, including managing users and deleting entries.
- **Editor** - can add and edit hostnames and databases, can view the
  audit log, cannot delete entries or manage users.
- **Viewer** - can log in and browse, nothing else.

If someone forgets their password, an admin resets it from the Users page
(same one-time temporary password flow). There's no email-based reset -
shared hosting mail is unreliable enough that a human doing this by hand
is the more dependable option for a small self-hosted site.

## 6. Add hostnames and databases

Use "+ Add hostname" on the main directory page. `tier` is machine, webapp
or service; `status` is live or planned; `on` machine ties a web app or
service back to whichever box runs it. See [below](#optional-hostname-suggestions)
if you want a naming-scheme picker on this form.

The **Databases** page works the same way but is intentionally separate
from the hostname directory, since a database usually isn't something you
open in a browser. It tracks name, engine (MySQL, PostgreSQL, Redis,
whatever), which machine it runs on, port, and what uses it - never
credentials. The notes field will refuse to save if it spots words like
"password" or "secret", as a guardrail, not a guarantee - don't rely on it
as your only safeguard.

## 7. Everything is logged

Every add, edit and delete of a hostname, database or user shows up on the
**Audit log** page (visible to admins and editors) with who did it and
when.

## Optional: hostname suggestions

Core ships with no naming scheme at all - not even the folder for one. The
"add hostname" form is just a plain form until you add one yourself. A
"naming scheme" here just means a word list the form can suggest from (it
only pre-fills the form; nothing is saved until you submit) - it has
nothing to do with the app's own appearance, which doesn't change.

To add one, create an `app/naming-schemes/` folder and drop a naming-scheme
file into it - a plain PHP file that `return`s an array of entries, each
with a `host` and a `tier` (`machine`, `webapp` or `service`) required,
plus optional `role`, `runs`, `why` and `notes` strings used only to
pre-fill the form. It's picked up automatically the next time someone
opens the "add hostname" form; delete the file (or the whole folder) and
its suggestions stop appearing.

You can have more than one naming-scheme file active at once - everything
directly inside `app/naming-schemes/` gets loaded and merged together,
processed in alphabetical order by filename (if two files suggest the
same hostname, the alphabetically earliest one wins).

Naming-scheme packs (ready-made word lists) live separately from the
deployed app - not inside `app/`, so they never get uploaded with it.
This repository's own `naming-schemes/` folder, at the repo root, has one
example: a woodland theme (trees for machines, places in a wood for web
apps, woodland creatures for services). It shares its name with the
folder the app itself scans (`app/naming-schemes/`) on purpose - you can
copy the whole `naming-schemes/` folder straight into `app/` on your
install, or just the file(s) inside it, and either way it ends up in the
right place: `app/naming-schemes/<file>.php`.

## Security notes

- Passwords are hashed with bcrypt (PHP's `password_hash`), never stored
  in plain text.
- The password policy follows [UK NCSC guidance](https://www.ncsc.gov.uk/collection/passwords)
  rather than the older "must contain a symbol" style of rule, which NCSC
  itself advises against. Every self-chosen password (the first admin
  account in `setup.php`, and anyone changing their own password in
  `account.php`) must be:
  - at least 12 characters (and no more than 72 - longer than that gets
    silently truncated by bcrypt, so it's rejected outright instead of
    quietly weakened);
  - not a common word or pattern ("welcome1234", "password123", etc are
    rejected on a local list, no network access needed for this part);
  - not equal to or containing the account's own username;
  - not a run of repeated or sequential characters ("aaaa", "1234");
  - and not a password known to appear in real-world data breaches. This
    last check calls [Have I Been Pwned's Pwned Passwords API](https://haveibeenpwned.com/Passwords)
    using k-anonymity - only the first 5 characters of the password's
    SHA-1 hash are ever sent, never the password itself or the rest of
    its hash. If that request fails for any reason (no outbound internet,
    the API is down, your host blocks it), the check is skipped rather
    than blocking account creation - a network hiccup should never lock
    someone out of setting a password.
  Temporary passwords the app generates for you (new accounts, admin
  resets) are random and skip all of this - they're already stronger than
  anything typed by hand.
- Five wrong password attempts locks an account for 15 minutes (NCSC
  recommends throttling login attempts instead of, or as well as, strict
  complexity rules). There's also no forced periodic password expiry
  anywhere in the app - NCSC advises against that too, and there's simply
  no such feature to disable.
- All data-changing requests are protected against CSRF and go through
  parameterised SQL queries.
- `.htaccess` forces HTTPS and blocks direct requests to `includes/` and
  to `.sql`/`.md` files.
- The page keeps `<meta name="robots" content="noindex, nofollow">` and an
  `X-Robots-Tag` header, so it never shows up in search results.

## Backups

phpMyAdmin -> your database -> **Export** -> Quick -> SQL, downloaded
periodically, is enough for a site this size. There's no automatic backup
built into the app itself.

## Troubleshooting

**500 error on every page.** Almost always `config.php` is missing or in
the wrong place. Re-check step 3's path - it goes in `private_html`, next
to whichever `public_html` your app's files are actually in, mirroring
the same subpath (see the worked examples in step 3).

**"Missing config.php" message.** Same as above, but PHP is at least
running - check the exact path you uploaded to against what
`whereami.php` (see step 3) printed.

**A blank white page instead of an error.** PHP error display is likely
off (correct for production) and something threw before it could log
cleanly. Check your host's PHP error log, and confirm the PHP version is
8.1+ (usually selectable somewhere like a "MultiPHP" or "PHP Selector"
tool in your control panel).

**Redirect loop, or the page won't load at all, right after uploading.**
Double-check step 2: the *contents* of `app/` need to sit directly at the
path you're browsing to (so `install.php` is at, say,
`public_html/arboretum/install.php`), not nested inside an extra `app`
folder (`public_html/arboretum/app/install.php`). Uploading the whole
`app` folder by mistake is an easy slip, especially if your FTP client
defaults to copying the folder itself rather than its contents. It's easy
to mistake for the HTTPS issue below because some hosts respond to a
path that doesn't exist with something that looks like a redirect loop
rather than a plain 404 - if `.htaccess` doesn't fix it, this misplaced
folder is the first thing to check.

**Redirect loop on every page load, even though the address bar already
shows `https://`.** Some hosts terminate SSL at a proxy/load balancer in
front of your actual server, so Apache's own `%{HTTPS}` check always
reads "off" no matter what the visitor's browser is doing - the `.htaccess`
included already checks the `X-Forwarded-Proto` header as well to cover
this, which is enough for most hosts. If you still get a loop after that,
your SSL certificate itself probably isn't active yet - confirm one is
issued for your domain in your hosting control panel, or temporarily
comment out the five `RewriteEngine`/`RewriteCond`/`RewriteCond`/`RewriteRule`
lines at the top of `.htaccess` until it is.

**Locked out of your only admin account.** Open phpMyAdmin, find your row
in the `users` table, and manually set `failed_attempts` to `0` and
`locked_until` to `NULL`. If you've genuinely forgotten the password
too, update `password_hash` directly to a bcrypt hash of a new one - PHP's
`password_hash('yournewpassword', PASSWORD_DEFAULT)` run anywhere you have
PHP available (even a quick local script) will produce one.
