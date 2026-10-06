# Deployment

This is a plain PHP + MySQL app.

## Required database variables

Set either:

- `DATABASE_URL` or `MYSQL_URL`

Or set all of these:

- `DB_HOST`
- `DB_PORT`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`

The local fallback is:

- database: `bac_system`
- user: `root`
- password: empty
- host: `localhost`
- port: `3306`

## Database setup

Import `bacll_system/database.sql` into the hosted MySQL database before sharing the app link.

Default admin login:

- username: `admin`
- password: `admin123`

## Docker hosting

The included `Dockerfile` serves the app from Apache with PHP 8.2 and enables the `mysqli` extension.
