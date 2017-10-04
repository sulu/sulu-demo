# Sulu - Demo Website

This is the official Sulu Demo. It was created to show a simple implementation of an application made 
with Sulu and explains the basic steps.

This project also runs here: https://sulu.rocks

For information about Sulu have a look at our Homepage:
http://sulu.io/

Our documentation is available under:
http://docs.sulu.io/

## Additional Bundles

### [SuluArticleBundle](https://github.com/sulu/SuluArticleBundle)

The SuluArticleBundle adds support for managing articles in Sulu. Articles can be used in a lot of different ways to manage unstructured data with an own URL in an admin-list. Most of the features, which can be used in pages, can also be used on articles - like templates, versioning, drafting, publishing and automation.

### [SuluAutomationBundle](https://github.com/sulu/SuluAutomationBundle)

The SuluAutomationBundle provides a way to manages future tasks which can be scheduled for entities in the Sulu-Admin. For example schedule the publishing of a page to a specific datetime in the future.

To enable automated tasks use the command ``task:run`` manually in the terminal or in a cronjob. This tasks executes the
pending automation tasks (see https://github.com/sulu/SuluAutomationBundle/blob/master/Resources/doc/installation.md).

### [SuluRedirectBundle](https://github.com/sulu/SuluRedirectBundle)

The SuluRedirectBundle adds support for managing redirects in Sulu-Admin.

## Requirements

* Mac OSX, Linux or Windows
* Apache or Nginx with enabled URL rewriting
* PHP 5.5 or higher
* the intl extension for PHP
* the gd or imagick extension for PHP
* a database management system supported by Doctrine
* Elasticsearch 5.0 or higher
* Composer
* Node.js and Compass (if you want to run a npm task)

## Installation

```
php composer create-project sulu/sulu-demo
cd sulu-demo
php bin/adminconsole app:install
```

### File Permissions

To fix the permissions of the project so that the web server is able to read and write them you need to run the following:

__Linux:__

```
rm -rf var/cache/*
rm -rf var/logs/*
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs var/uploads var/uploads/* web/uploads web/uploads/* var/indexes var/sessions
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs var/uploads var/uploads/* web/uploads web/uploads/* var/indexes var/sessions
```

__Mac:__

```
rm -rf var/cache/*
rm -rf var/logs/*
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo chmod +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" var/cache var/logs var/uploads var/uploads/* web/uploads web/uploads/* var/indexes var/sessions var/sessions/*
sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" var/cache var/logs var/uploads var/uploads/* web/uploads web/uploads/* var/indexes var/sessions var/sessions/*
```

__Windows (with IIS web server):__

```
rm -rf var/cache/*
rm -rf var/logs/*
$rule = New-Object System.Security.AccessControl.FileSystemAccessRule -ArgumentList @("IUSR","FullControl","ObjectInherit, ContainerInherit","None","Allow")
$folders = "var\cache", "var\logs", "var\indexes", "var\sessions", "var\uploads", "var\uploads\*", "web\uploads", "web\uploads\*"
foreach ($f in $folders) { $acl = Get-Acl $f; $acl.SetAccessRule($rule); Set-Acl $f $acl; }
```

## Usage

Now you can try out our demo, there is no need to configure a virtual host. Just use the build in web servers:

```
bin/adminconsole server:start
bin/websiteconsole server:start
```

Then you can access the administration interface via http://127.0.0.1:8000/admin. The default user and password is “admin”.

The web frontend can be found under http://127.0.0.1:8001.

## Tests

**Initialize test database**

```bash
bin/adminconsole doctrine:database:create --env=test
bin/adminconsole doctrine:schema:update --force --env=test
```

**Running tests**

```bash
bin/simple-phpunit
```

## Questions? We have answers!

We've got a #Slack channel where you can talk directly to strategists, developers and designers. Just contact us under http://sulu.io/#questions.
