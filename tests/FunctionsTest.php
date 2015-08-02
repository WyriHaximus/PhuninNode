<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests;

use React\Promise\FulfilledPromise;
use WyriHaximus\PhuninNode\Value;

/**
 * Class FunctionsTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testValuePromisesToObjectStorageAndTestArrayToValuePromises()
    {
        $promises = [
            ['a', 'b'],
            ['c', 'd'],
            ['e', 'f'],
        ];

        \WyriHaximus\PhuninNode\valuePromisesToObjectStorage(\WyriHaximus\PhuninNode\arrayToValuePromises($promises))->then(function (\SplObjectStorage $storage) {
            $storage->rewind();
            $this->assertSame(3, $storage->count());
            $this->assertSame([
                'a',
                'b',
            ], [
                $storage->current()->getKey(),
                $storage->current()->getValue(),
            ]);
            $this->assertSame([
                'c',
                'd',
            ], [
                $storage->current()->getKey(),
                $storage->current()->getValue(),
            ]);
            $this->assertSame([
                'e',
                'f',
            ], [
                $storage->current()->getKey(),
                $storage->current()->getValue(),
            ]);
        });
    }
}
