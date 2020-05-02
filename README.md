# Sulu - Demo Website

This is the official Sulu Demo. It was created to show a simple implementation of an application made 
with Sulu and explains the basic steps.

This project also runs here: [https://sulu.rocks](https://sulu.rocks)

For information about Sulu have a look at our Homepage:
[http://sulu.io/](http://sulu.io/)

Our documentation is available under:
[http://docs.sulu.io/](http://docs.sulu.io/)

## Used Extensions

### [SuluArticleBundle](https://github.com/sulu/SuluArticleBundle)

The SuluArticleBundle adds support for managing articles in Sulu. Articles can be used in a lot of different ways to manage unstructured data with an own URL in an admin-list.
Most of the features, which can be used in pages, can also be used on articles - like templates, versioning, drafting, publishing and automation.

### [SuluAutomationBundle](https://github.com/sulu/SuluAutomationBundle)

The SuluAutomationBundle provides a way to manages future tasks which can be scheduled for entities in the Sulu-Admin. For example schedule the publishing of a page to a specific datetime in the future.

To enable automated tasks use the command ``task:run`` manually in the terminal or in a cronjob. This tasks executes the
pending automation tasks (see [SuluAutomationBundle Installation Docs](https://github.com/sulu/SuluAutomationBundle/blob/master/Resources/doc/installation.md)).

### [SuluWebTwig](https://github.com/sulu/web-twig) and [SuluWebJS](https://github.com/sulu/web-js)

A collection of helpful twig extensions and a tiny js component mangaement library.

## Requirements

* MySQL or PostgreSQL Server
* PHP 7.3 or higher
    - json extension
    - xml extension
    - simplexml extension
    - gd or imagick extension (needed for image converts)
* Composer
* NPM if you want to run npm tasks

## Installation

```bash
git clone git@github.com:sulu/sulu-demo.git
cd sulu-demo
composer install
```

Configure now your `DATABASE_URL` in the `.env.local` see `.env` as reference.

Install the demo with all fixtures by running:

```bash
bin/console sulu:build dev
```

## Usage

Now you can try out our demo, there is no need to configure a virtual host. Just use the build in web servers:

```bash
bin/console server:run
```

Then you can access the administration interface via [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin). The default user and password is “admin”.

The web frontend can be found under [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Tests

```bash
composer bootstrap-test-environment
composer lint
composer test
```

## Questions? We have answers!

We've got a [#Slack](https://sulu.io/community#chat) channel where you can talk directly to strategists, developers and designers.
