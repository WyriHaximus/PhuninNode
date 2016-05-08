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

use React\EventLoop\Factory;
use WyriHaximus\PhuninNode\Value;
use function Clue\React\Block\await;
use function WyriHaximus\PhuninNode\arrayToValuePromises;
use function WyriHaximus\PhuninNode\arrayToMetricPromises;
use function WyriHaximus\PhuninNode\metricPromisesToObjectStorage;

/**
 * Class FunctionsTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testValuePromisesToObjectStorageAndTestArrayToValuePromises()
    {
        $array = [
            'a' => 'b',
            'c' => 'd',
            'e' => 'f',
        ];

        $storage = await(metricPromisesToObjectStorage(iterator_to_array(arrayToValuePromises($array))), Factory::create(), 5);
        $storage->rewind();
        $this->assertSame(3, $storage->count());
        $this->assertInstanceOf(Value::class, $storage->current());
        $this->assertSame([
            'a',
            'b',
        ], [
            $storage->current()->getKey(),
            $storage->current()->getValue(),
        ]);
        $storage->next();
        $this->assertInstanceOf(Value::class, $storage->current());
        $this->assertSame([
            'c',
            'd',
        ], [
            $storage->current()->getKey(),
            $storage->current()->getValue(),
        ]);
        $storage->next();
        $this->assertInstanceOf(Value::class, $storage->current());
        $this->assertSame([
            'e',
            'f',
        ], [
            $storage->current()->getKey(),
            $storage->current()->getValue(),
        ]);
    }
}
