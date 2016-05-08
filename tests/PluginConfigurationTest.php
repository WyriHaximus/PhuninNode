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

use WyriHaximus\PhuninNode\Configuration;

/**
 * Class PluginConfigurationTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class PluginConfigurationTest extends \PHPUnit_Framework_TestCase
{
    private $testData = [
        [
            'key' => 'a',
            'value' => 1,
        ],
        [
            'key' => 'b',
            'value' => 2,
        ],
        [
            'key' => 'c',
            'value' => 3,
        ],
        [
            'key' => 'd',
            'value' => 4,
        ],
        [
            'key' => 'e',
            'value' => 5,
        ],
    ];

    public function testPairs()
    {
        $configuration = new Configuration([
            'v' => 'w',
            'y' => 'z',
        ]);
        $this->assertEquals(2, count($configuration->getPairs()));
        $configuration = new Configuration();
        $this->assertEquals(0, count($configuration->getPairs()));
        for ($i = 0; $i < count($this->testData); $i++) {
            $configuration->setPair($this->testData[$i]['key'], $this->testData[$i]['value']);
            $this->assertEquals(($i + 1), count($configuration->getPairs()));
            $pair = $configuration->getPair($this->testData[$i]['key']);
            $this->assertEquals('WyriHaximus\PhuninNode\Value', get_class($pair));
            $this->assertEquals($this->testData[$i]['key'], $pair->getKey());
            $this->assertEquals($this->testData[$i]['value'], $pair->getValue());
        }
    }
}
