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

* PHP >= 7.1

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

$paths = [
    '/foo/bar/sample.txt', 
    '/baz/biz/example.txt', 
    '/fiz/boo/music.mp3',
];

// Find matches for the glob pattern `/**/*.txt`
$regex = glob_to_regex('/**/*.txt');

$matches = array_filter($paths, function ($path) use ($regex) {
    return preg_match($regex, $path);
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
