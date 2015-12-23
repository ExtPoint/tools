Builds cleaner
--------------

Script for clean old builds on server. PHP script moved old build to trash dir, bash - removed from file system.

## Install

Add clean script to root project folder

```
/var/www/project-name/
    config/
    current/
    tmp/
        trash/
    v3.1-105
    v3.1-101
    v3.0-98
    v3.0-95
    clean.php
```

Create trash directory

```sh
mkdir /var/www/project-name/tmp/trash
chmod 777 /var/www/project-name/tmp/trash
```

Add cron task

```sh
0 0 * * * php /var/www/project-name/clean.php m && rm -rf /var/www/project-name/tmp/trash/*
```

## Show script result (without execute)

```sh
php /var/www/project-name/clean.php
```

Result example:

```
Note: Append 'm' argument for real move dirs to trash.

Current version:
  /var/www/hoc2015/v3.1-125
Last versions:
  /var/www/hoc2015/v3.1-124
  /var/www/hoc2015/v3.1-123
Old versions (will be move):
  /var/www/hoc2015/v3.1-122
```