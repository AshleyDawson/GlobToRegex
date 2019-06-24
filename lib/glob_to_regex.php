<?php

namespace AshleyDawson\GlobToRegex;

/**
 * Glob matches globbing patterns against text.
 *
 *     if match_glob("foo.*", "foo.bar") echo "matched\n";
 *
 *     // prints foo.bar and foo.baz
 *     $regex = glob_to_regex("foo.*");
 *     for (['foo.bar', 'foo.baz', 'foo', 'bar'] as $t)
 *     {
 *         if (/$regex/) echo "matched: $car\n";
 *     }
 *
 * Glob implements glob(3) style matching that can be used to match
 * against text, rather than fetching names from a filesystem.
 *
 * Based on the Perl Text::Glob module.
 *
 * @param string $glob The glob pattern
 * @param bool $strictLeadingDot
 * @param bool $strictWildcardSlash
 * @param string $delimiter Optional delimiter
 * @return string
 *
 * @copyright 2019 Ashley Dawson <ashley@ashleydawson.co.uk>
 * @copyright 2004-2005 Fabien Potencier <fabien@symfony.com>
 * @copyright 2002 Richard Clamp <richardc@unixbeard.net>
 * @author Richard Clamp <richardc@unixbeard.net> Perl version
 * @author Fabien Potencier <fabien@symfony.com> PHP port
 * @author Ashley Dawson <ashley@ashleydawson.co.uk> Extraction to function
 */
function glob_to_regex(string $glob, bool $strictLeadingDot = true, bool $strictWildcardSlash = true, string $delimiter = '#'): string
{
    $firstByte = true;
    $escaping = false;
    $inCurlies = 0;
    $regex = '';
    $sizeGlob = \strlen($glob);

    for ($i = 0; $i < $sizeGlob; ++$i) {
        $car = $glob[$i];

        if ($firstByte && $strictLeadingDot && '.' !== $car) {
            $regex .= '(?=[^\.])';
        }

        $firstByte = '/' === $car;

        if ($firstByte && $strictWildcardSlash && isset($glob[$i + 2]) && '**' === $glob[$i + 1].$glob[$i + 2] && (!isset($glob[$i + 3]) || '/' === $glob[$i + 3])) {
            $car = '[^/]++/';

            if (!isset($glob[$i + 3])) {
                $car .= '?';
            }

            if ($strictLeadingDot) {
                $car = '(?=[^\.])'.$car;
            }

            $car = '/(?:'.$car.')*';
            $i += 2 + isset($glob[$i + 3]);

            if ('/' === $delimiter) {
                $car = str_replace('/', '\\/', $car);
            }
        }

        if ($delimiter === $car || '.' === $car || '(' === $car || ')' === $car || '|' === $car || '+' === $car || '^' === $car || '$' === $car) {
            $regex .= "\\$car";
        } elseif ('*' === $car) {
            $regex .= $escaping ? '\\*' : ($strictWildcardSlash ? '[^/]*' : '.*');
        } elseif ('?' === $car) {
            $regex .= $escaping ? '\\?' : ($strictWildcardSlash ? '[^/]' : '.');
        } elseif ('{' === $car) {
            $regex .= $escaping ? '\\{' : '(';

            if (!$escaping) {
                ++$inCurlies;
            }
        } elseif ('}' === $car && $inCurlies) {
            $regex .= $escaping ? '}' : ')';

            if (!$escaping) {
                --$inCurlies;
            }
        } elseif (',' === $car && $inCurlies) {
            $regex .= $escaping ? ',' : '|';
        } elseif ('\\' === $car) {
            if ($escaping) {
                $regex .= '\\\\';
                $escaping = false;
            } else {
                $escaping = true;
            }

            continue;
        } else {
            $regex .= $car;
        }

        $escaping = false;
    }

    return $delimiter.'^'.$regex.'$'.$delimiter;
}
