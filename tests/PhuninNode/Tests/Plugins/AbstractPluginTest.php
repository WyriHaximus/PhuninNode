<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhuninNode\Tests\Plugins;

abstract class AbstractPluginTest extends \PhuninNode\Tests\AbstractConnectionTest {
    
    protected $plugin;
    
    public function setUp() {
        parent::setUp();
    }
    
    public function testPlugin() {
        $classImplements = class_implements($this->plugin);
        $this->assertTrue(isset($classImplements['PhuninNode\Interfaces\Plugin']));
    }
    
    public function testConfiguration() {
        $this->assertInstanceOf('PhuninNode\PluginConfiguration', $this->plugin->getConfiguration());
        $this->assertInstanceOf('PhuninNode\PluginConfiguration', $this->plugin->getConfiguration());
    }
    
    public function testConfigurationValues() {
        foreach ($this->plugin->getConfiguration() as $value) {
            $this->assertInstanceOf('PhuninNode\Value', $value);
        }
    }
    
    public function testTwoFetchCalls() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('fetch ' . $that->plugin->getSlug() . PHP_EOL);
                    break;
                case 1:
                    $that->conn->write('fetch ' . $that->plugin->getSlug() . PHP_EOL);
                    break;
                case 2:
                    $that->loop->stop();
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testTwoConfigCalls() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('config ' . $that->plugin->getSlug() . PHP_EOL);
                    break;
                case 1:
                    $that->conn->write('config ' . $that->plugin->getSlug() . PHP_EOL);
                    break;
                case 2:
                    $this->loop->stop();
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function tearDown() {
        parent::tearDown();
    }
    
}