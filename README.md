<h1 align="center">Sulu - Demo Website</h1>

This is the official **Sulu Demo**. It was created to show a simple implementation of an application made
with Sulu and explains the basic steps.

This project also runs here: [https://sulu.rocks](https://sulu.rocks)

For information about Sulu have a look at our Homepage:
[http://sulu.io/](http://sulu.io/)

Our documentation is available under:
[http://docs.sulu.io/](http://docs.sulu.io/)

<br/>
<p align="center">
    <img width="80%" src="https://sulu.io/uploads/media/800x@2x/01/251-sulu-demo.gif?v=1-0" alt="Sulu Demo Slideshow">
</p>
<br/>

## Used Extensions

### Articles

Articles are part of the Sulu core package since Sulu 3.0, the separate SuluArticleBundle is gone.
They manage content with an own URL in an admin list and support most of what pages support:
templates, versioning, drafting, publishing and automation.

### [SuluAutomationBundle](https://github.com/sulu/SuluAutomationBundle)

The SuluAutomationBundle provides a way to manages future tasks which can be scheduled for entities in the Sulu-Admin. For example schedule the publishing of a page to a specific datetime in the future.

To enable automated tasks use the command ``task:run`` manually in the terminal or in a cronjob. This tasks executes the
pending automation tasks (see [SuluAutomationBundle Installation Docs](https://github.com/sulu/SuluAutomationBundle/blob/master/Resources/doc/installation.md)).

### [SuluWebTwig](https://github.com/sulu/web-twig) and [SuluWebJS](https://github.com/sulu/web-js)

A collection of helpful twig extensions and a tiny js component mangaement library.

## Requirements

* PHP 8.2
    - json extension
    - xml extension
    - simplexml extension
    - pdo_sqlite extension (used by the Loupe search adapter)
    - gd or imagick extension (needed for image converts)
* MySQL or PostgreSQL Server
* Composer
* NPM if you want to run npm tasks

## Installation

```bash
git clone git@github.com:sulu/sulu-demo.git
cd sulu-demo
composer install
```

### Configure required services

The demo requires a running **MySQL** instance. Search runs on SEAL with the Loupe adapter, which
only needs SQLite, so no separate search server is required.

Configure your `DATABASE_URL` in the `.env.local`  see `.env` as reference.

If you don't want to install the services yourself you can use the provided [docker-compose.yml](https://docs.docker.com/compose/install/)
to start this services inside an own container:

```bash
docker compose up
```

### Install fixtures

Install the demo with all fixtures by running:

```bash
bin/console sulu:build dev
```

## Usage

Now you can try out our demo, there is no need to configure a virtual host. Just use the build in web servers:

```bash
php -S 127.0.0.1:8000 -t public config/router.php
```

Then you can access the administration interface via [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin). The default user and password is “admin”.

The web frontend can be found under [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Upgrading an existing Sulu 2.6 installation

The demo runs on Sulu 3.0, which stores content in database tables instead of PHPCR. An existing
2.6 database is migrated in three steps: rename `ro_routes` to `ro_routes_old`, run
`bin/console doctrine:migrations:migrate` to create the new content tables, and move the content
with [SuluPhpcrMigrationBundle](https://github.com/sulu/SuluPHPCRMigrationBundle):

```bash
bin/adminconsole sulu:phpcr-migration:migrate --dry-run
bin/adminconsole sulu:phpcr-migration:migrate
bin/adminconsole cmsig:seal:reindex
```

`doctrine:schema:update` must not be used on Sulu 3.0, the schema comes from the shipped
migrations. The `phpcr_*` tables and `ro_routes_old` can be dropped once the migrated content
has been verified.

## Tests

```bash
composer bootstrap-test-environment
composer lint
composer test
```

## Questions? We have answers!

We've got a [#Slack](https://sulu.io/community#chat) channel where you can talk directly to strategists, developers and designers.
