<?php
declare(strict_types=1);

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests;

use WyriHaximus\PhuninNode\EchoLogger;

/**
 * Class EchoLoggerTest
 * @package WyriHaximus\PhuninNode
 */
class EchoLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        $logger = new EchoLogger();
        ob_start();
        $logger->log('abc', 'def');
        $this->assertSame('[abc] def' . PHP_EOL, ob_get_clean());
    }
}
