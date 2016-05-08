<?php
declare(strict_types=1);

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests;

use WyriHaximus\PhuninNode\Value;

/**
 * Class ValueTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $value = new Value('key', 'value');
        $this->assertEquals('key', $value->getKey());
        $this->assertEquals('value', $value->getValue());
    }

}
