<?php

namespace AshleyDawson\GlobToRegex\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use function AshleyDawson\GlobToRegex\glob_to_regex;

/**
 * Class GlobToRegexTest
 *
 * @package AshleyDawson\GlobToRegex\Tests
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Ashley Dawson <ashley@ashleydawson.co.uk>
 * @copyright Fabien Potencier <fabien@symfony.com>
 * @copyright Ashley Dawson <ashley@ashleydawson.co.uk>
 */
class GlobToRegexTest extends TestCase
{
    public function testGlobToRegexDelimiters()
    {
        $this->assertEquals('#^(?=[^\.])\#$#', glob_to_regex('#'));
        $this->assertEquals('#^\.[^/]*$#', glob_to_regex('.*'));
        $this->assertEquals('^\.[^/]*$', glob_to_regex('.*', true, true, ''));
        $this->assertEquals('/^\.[^/]*$/', glob_to_regex('.*', true, true, '/'));
    }

    public function testGlobToRegexDoubleStarStrictDots()
    {
        $finder = new Finder();
        $finder->ignoreDotFiles(false);
        $regex = glob_to_regex('/**/*.neon');

        foreach ($finder->in(__DIR__) as $k => $v) {
            $k = str_replace(\DIRECTORY_SEPARATOR, '/', $k);
            if (preg_match($regex, substr($k, \strlen(__DIR__)))) {
                $match[] = substr($k, 10 + \strlen(__DIR__));
            }
        }
        sort($match);

        $this->assertSame(['one/b/c.neon', 'one/b/d.neon'], $match);
    }

    public function testGlobToRegexDoubleStarNonStrictDots()
    {
        $finder = new Finder();
        $finder->ignoreDotFiles(false);
        $regex = glob_to_regex('/**/*.neon', false);

        foreach ($finder->in(__DIR__) as $k => $v) {
            $k = str_replace(\DIRECTORY_SEPARATOR, '/', $k);
            if (preg_match($regex, substr($k, \strlen(__DIR__)))) {
                $match[] = substr($k, 10 + \strlen(__DIR__));
            }
        }
        sort($match);

        $this->assertSame(['.dot/b/c.neon', '.dot/b/d.neon', 'one/b/c.neon', 'one/b/d.neon'], $match);
    }

    public function testGlobToRegexDoubleStarWithoutLeadingSlash()
    {
        $finder = new Finder();
        $finder->ignoreDotFiles(false);
        $regex = glob_to_regex('/Fixtures/one/**');

        foreach ($finder->in(__DIR__) as $k => $v) {
            $k = str_replace(\DIRECTORY_SEPARATOR, '/', $k);
            if (preg_match($regex, substr($k, \strlen(__DIR__)))) {
                $match[] = substr($k, 10 + \strlen(__DIR__));
            }
        }
        sort($match);

        $this->assertSame(['one/a', 'one/b', 'one/b/c.neon', 'one/b/d.neon'], $match);
    }

    public function testGlobToRegexDoubleStarWithoutLeadingSlashNotStrictLeadingDot()
    {
        $finder = new Finder();
        $finder->ignoreDotFiles(false);
        $regex = glob_to_regex('/Fixtures/one/**', false);

        foreach ($finder->in(__DIR__) as $k => $v) {
            $k = str_replace(\DIRECTORY_SEPARATOR, '/', $k);
            if (preg_match($regex, substr($k, \strlen(__DIR__)))) {
                $match[] = substr($k, 10 + \strlen(__DIR__));
            }
        }
        sort($match);

        $this->assertSame(['one/.dot', 'one/a', 'one/b', 'one/b/c.neon', 'one/b/d.neon'], $match);
    }
}
