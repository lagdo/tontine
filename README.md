Siak Tontine
============

Web application for online tontine management.

Made with [Laravel](https://laravel.com) and [Jaxon](https://www.jaxon-php.org).

**This package development is still in progress. It is not yet ready for production.**

Installation
------------

After downloading this package, install the dependencies with `Composer`.

```bash
composer install
```

Set the database name and credentials in the `.env` file.

Create the database tables.

```bash
php artisan migrate
```

Seed the database with the default data (user, country and currency).

```bash
php artisan db:seed
```

Configure the web server to publish files in the `public` subdir, and display the page in your browser.
