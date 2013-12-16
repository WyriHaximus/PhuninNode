<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests;

class PluginConfigurationTest extends \PHPUnit_Framework_TestCase {
    
    private $Value;
    
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
    
    public function setUp() {
        parent::setUp();
        $this->PluginConfiguration = new \WyriHaximus\PhuninNode\PluginConfiguration();
    }
    
    public function testPairs() {
        $this->assertEquals(0, count($this->PluginConfiguration->getPairs()));
        for ($i = 0; $i < count($this->testData); $i++) {
            $this->PluginConfiguration->setPair($this->testData[$i]['key'], $this->testData[$i]['value']);
            $this->assertEquals(($i + 1), count($this->PluginConfiguration->getPairs()));
            $pair = $this->PluginConfiguration->getPair($this->testData[$i]['key']);
            $this->assertEquals('WyriHaximus\PhuninNode\Value', get_class($pair));
            $this->assertEquals($this->testData[$i]['key'], $pair->getKey());
            $this->assertEquals($this->testData[$i]['value'], $pair->getValue());
        }
    }
    
    public function tearDown() {
        unset($this->PluginConfiguration);
        parent::tearDown();
    }
    
}