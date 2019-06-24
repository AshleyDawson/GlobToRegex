Glob to Regex
=============

[![Build Status](https://travis-ci.org/AshleyDawson/GlobToRegex.svg?branch=master)](https://travis-ci.org/AshleyDawson/GlobToRegex)

PHP function that converts a glob pattern into a regular expression, extracted 
from the [Symfony Finder](https://github.com/symfony/finder) component.

Installation
------------

To install via Composer, do the following:

```bash
$ composer req ashleydawson/globtoregex
```

Requirements are:

* PHP >= 7.0

Basic Usage
-----------

Basic usage of the function is as follows:

```php
<?php

require __DIR__.'/vendor/autoload.php';

$regex = \AshleyDawson\GlobToRegex\glob_to_regex('/**/*.txt');

echo $regex;
```

Where output is:

```text
#^(?=[^\.])/(?:(?=[^\.])[^/]++/)*(?=[^\.])[^/]*\.txt$#
```

Usage
-----

The following simple example returns a set of matched file paths:

```php
<?php

require __DIR__.'/vendor/autoload.php';

use function AshleyDawson\GlobToRegex\glob_to_regex;

// Find matches for the glob pattern `/**/*.txt`
$matches = array_filter(['/foo/bar/sample.txt', '/baz/biz/example.txt', '/fiz/boo/music.mp3'], function ($path) {
    return preg_match(glob_to_regex('/**/*.txt'), $path);
});

print_r($matches);
```

Where output is:

```text
Array
(
    [0] => /foo/bar/sample.txt
    [1] => /baz/biz/example.txt
)
```

Testing
-------

To run the test suite, do the following:

```bash
$ vendor/bin/phpunit -c .
```
