# Arboretum

A small self-hosted web app for tracking the hostnames and databases on
your network: what each machine, web app and service is called, what it
runs, and where it lives - plus a login page, roles, and an audit log, so
it's safe to give more than one person access.

Built to run on ordinary shared hosting (PHP + MySQL/MariaDB) with no
build step, no framework, and no dependencies to install.

## Features

- **Hostname directory** - machines, web apps and services, each with a
  status (live/planned), what runs on it, and why it's named what it's
  named. Search, tier and status filters, card layout.
- **Database directory** - a separate page for logging databases (engine,
  host machine, port, purpose). No credentials are ever stored here, and
  the notes field refuses to save text that looks like a password or
  secret, as a guardrail.
- **Login and roles** - Admin (full access, manages users), Editor (add/
  edit, no delete), Viewer (read-only).
- **Audit log** - every add, edit and delete is recorded with who and
  when, visible to admins and editors.
- **GUI installer** - `install.php` walks you through database setup the
  same way Roundcube's installer does: enter your DB details, it tests
  the connection, creates the tables, and writes `config.php` for you.
- Nothing is pre-filled. The directory starts empty, and there's no
  built-in naming scheme at all - it's a pure extension point (see below)
  for naming-scheme packs distributed separately, not part of this repo.

## Repository layout

```
app/            The application itself - upload the contents of this
                folder to your host's public_html (see SETUP.md).
  includes/     Shared PHP: database access, auth/sessions, CSRF, audit
                logging, password policy, page layout.
  assets/       CSS and the client-side search/filter JS.
  install.php   One-time GUI installer (creates config.php + DB tables).
  setup.php     One-time first-admin-account creator, run after install.
  config.example.php   Template for config.php, for a manual install -
                install.php normally writes this for you (see SETUP.md).
db/schema.sql   The database schema, for manual import via phpMyAdmin
                if you'd rather not use install.php.
naming-schemes/ Ready-made naming-scheme files (see below) - not part of
                the deployed app, and not uploaded with app/. Staged here
                for packaging as a separate add-on repository later.
SETUP.md        Full deployment walkthrough, start to finish.
```

## Getting started

See [SETUP.md](SETUP.md) for the full walkthrough. In short:

1. Create a MySQL database in your hosting control panel.
2. Upload the contents of `app/` to `public_html`.
3. Visit `install.php` in your browser and fill in your database details.
4. Delete `install.php`, then visit `setup.php` to create your first
   admin account, and delete that too once you're logged in.
5. Add other users from the Users page, then start filling in hostnames
   and databases.

## Optional: hostname suggestions

The "add hostname" form can show an optional pick-a-name helper (it only
pre-fills the form; nothing is saved until you submit) - a "naming
scheme" here is just a word list, unrelated to the app's own visual
appearance, which doesn't change. Core ships with no naming scheme at all,
not even the folder for one - it's a pure extension point.

To add one, create an `app/naming-schemes/` folder and drop a naming-scheme
file into it - a plain PHP file that `return`s an array of entries, each
with a `host` and a `tier` (`machine`/`webapp`/`service`) required, plus
optional `role`, `runs`, `why` and `notes` strings. It's picked up
automatically; delete the file (or the folder) to turn it off again. You
can have several naming-scheme files active at once; everything in that
folder gets merged together.

This repository's own `naming-schemes/` folder (at the repo root, not
inside `app/`) has one ready-made example - copy a file from there into
`app/naming-schemes/` on your install to use it.

## Tech stack

Plain PHP (8.1+) and MySQL/MariaDB, no framework, no Composer
dependencies, no build step. Chosen deliberately so it runs unmodified on
ordinary shared hosting: PDO with prepared statements throughout, bcrypt
password hashing, CSRF tokens on every form, and sessions for auth.

## Security

Passwords are hashed with bcrypt and never stored in plain text; the
password policy follows [UK NCSC guidance](https://www.ncsc.gov.uk/collection/passwords) -
a self-chosen password must be 12-72 characters, isn't a common word or
pattern, doesn't contain the username, and is checked against Have I Been
Pwned's breach database via its privacy-preserving k-anonymity API (only
a 5-character hash prefix is ever sent; the check fails open if it can't
be reached). Admin-generated temporary passwords are random and skip all
of this, they're already stronger. Repeated failed logins lock an
account for 15 minutes; every write request is
CSRF-protected and uses parameterised SQL; `config.php` (which holds your
database credentials) lives in a `private_html` folder next to
`public_html`, never inside it, so no URL can ever serve it; `.htaccess`
forces HTTPS and blocks direct access to the `includes/` folder. See the
Security notes section of `SETUP.md` for more.

## License

MIT - see [LICENSE](LICENSE).

## Group Policies

This repository is governed by the Fenstream Technology Group policy baseline:  
https://github.com/fenstreamtech/.github/blob/main/GROUP_POLICY.md

