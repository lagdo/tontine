[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lagdo/tontine/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/lagdo/tontine/?branch=main)
[![StyleCI](https://github.styleci.io/repos/500135711/shield?branch=main)](https://github.styleci.io/repos/500135711?branch=main)

[![Latest Stable Version](https://poser.pugx.org/lagdo/tontine/v/stable)](https://packagist.org/packages/lagdo/tontine)
[![License](https://poser.pugx.org/lagdo/tontine/license)](https://packagist.org/packages/lagdo/tontine)

Siak Tontine
============

Web application for online tontine management.

Made with [Laravel](https://laravel.com) and [Jaxon](https://www.jaxon-php.org).

A demo is [available here](https://demo.ton.siakapp.net).

Installation
------------

After downloading this package, install the dependencies with `Composer`.

```bash
composer install
php artisan key:generate
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
