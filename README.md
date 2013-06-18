RouterOS API
============

About
-----
- A composer package for Mikrotik / Router OS API
- Originally from: http://wiki.mikrotik.com/wiki/API_PHP_class

Installation
-----

###Composer

Incorperate the following to your `composer.json` file
```json
"repositories": [
    {
        "type": "git",
        "name": "tnoc/routeros",
        "url": "http://github.com/TNOC/routeros_api"
    }
],
"require": {
    "tnoc/routeros": "dev-master"
}
```

Then update your dependencies: `php composer.phar update`

Usage
------

Example index.php

```php
<?php

require_once "vendor/autoload.php";

use \RouterOS;

$api = new RouterOS\Core();

print_r($api);
```

Executables
----

###./bin/routeros-test.php

routeros test is a script to test that a Mikrotik is accessable through API

<b>Usage:</b>
- php ./bin/routeros-test.php --help
- php ./bin/routeros-test.php -h 111.111.111.111 -u username

Examples
----

Examples are from http://wiki.mikrotik.com/wiki/API_PHP_class

- [Example 1](https://github.com/TNOC/routeros_api/wiki/Example-1)
- [Example 2](https://github.com/TNOC/routeros_api/wiki/Example-2)
