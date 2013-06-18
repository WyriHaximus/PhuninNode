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

abstract class AbstractPluginTest extends \PhuninNode\Tests\AbstractConnectionContextTest {
    
    protected $plugin;
    
    public function setUp() {
        parent::setUp();
    }
    
    public function testPlugin() {
        $classImplements = class_implements($this->plugin);
        $this->assertTrue(isset($classImplements['PhuninNode\Interfaces\Plugin']));
    }
    
    public function testConfiguration() {
        
        
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(function($configuration) {
            $this->assertInstanceOf('PhuninNode\PluginConfiguration', $configuration);
        });
        $this->plugin->getConfiguration($deferred->resolver());
        
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(function($configuration) {
            $this->assertInstanceOf('PhuninNode\PluginConfiguration', $configuration);
        });
        $this->plugin->getConfiguration($deferred->resolver());
    }
    
    public function testConfigurationValues() {
        
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(function($configuration) {
            foreach ($configuration as $value) {
                $this->assertInstanceOf('PhuninNode\Value', $value);
            }
        });
        $this->plugin->getConfiguration($deferred->resolver());
    }
    
    public function testTwoFetchCalls() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('fetch ' . $this->plugin->getSlug() . PHP_EOL);
                    break;
                case 1:
                    $this->conn->write('fetch ' . $this->plugin->getSlug() . PHP_EOL);
                    break;
                case 2:
                    $this->loop->stop();
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testTwoConfigCalls() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('config ' . $this->plugin->getSlug() . PHP_EOL);
                    break;
                case 1:
                    $this->conn->write('config ' . $this->plugin->getSlug() . PHP_EOL);
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