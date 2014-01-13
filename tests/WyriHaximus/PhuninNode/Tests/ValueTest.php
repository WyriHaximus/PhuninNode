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

/**
 * Class ValueTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{

    private $Value;

    public function setUp()
    {
        parent::setUp();
        $this->Value = new \WyriHaximus\PhuninNode\Value();
    }

    public function testConstruct()
    {
        $this->Value = new \WyriHaximus\PhuninNode\Value(1, 2);
        $this->assertEquals(1, $this->Value->getKey());
        $this->assertEquals(2, $this->Value->getValue());
    }

    public function testKey()
    {
        $this->assertEquals(null, $this->Value->getKey());
        $this->Value->setKey(true);
        $this->assertEquals(true, $this->Value->getKey());
    }

    public function testValue()
    {
        $this->assertEquals(null, $this->Value->getValue());
        $this->Value->setValue(true);
        $this->assertEquals(true, $this->Value->getValue());
    }

    public function tearDown()
    {
        unset($this->Value);
        parent::tearDown();
    }
}
