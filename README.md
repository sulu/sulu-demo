# Sulu - Demo Website

This is the official Sulu Demo. It was created to show a simple implementation of an application made 
with Sulu and explains the basic steps.

For information about Sulu have a look at our Homepage:
http://sulu.io/

Our documentation is available under:
http://docs.sulu.io/


## Requirements

* Mac OSX, Linux or Windows
* Apache or Nginx with enabled URL rewriting
* PHP 5.5 or higher
* the intl extension for PHP
* the gd or imagick extension for PHP
* a database management system supported by Doctrine
* Composer
* Node.js and Compass (if you want to run a npm task)

## Installation

```
composer install
bin/adminconsole sulu:build dev
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

## Questions? We have answers!

We've got a #Slack channel where you can talk directly to strategists, developers and designers. Just contact us under http://sulu.io/#questions.
