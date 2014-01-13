<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests\Plugins;

/**
 * Class AbstractPluginTest
 * @package WyriHaximus\PhuninNode\Tests\Plugins
 */
abstract class AbstractPluginTest extends \PHPUnit_Framework_TestCase
{

    protected $plugin;

    public function setUp()
    {
        parent::setUp();

        $this->loop = $this->getMock('\React\EventLoop\StreamSelectLoop');
        $this->socket = $this->getMock(
            '\React\Socket\Server',
            [
                'on',
                'write',
            ],
            [
                $this->loop,
                '0.0.0.0',
                12345
            ]
        );

        $this->node = $this->getMock(
            '\WyriHaximus\PhuninNode\Node',
            [
                'getPlugins',
                'getPlugin',
                'getValues',
                'addPlugin',
            ],
            [
                $this->loop,
                $this->socket,
            ]
        );

        $this->plugin->setNode($this->node);

        $this->plugins = new \SplObjectStorage();
        $plugin = $this->getMock(
            '\WyriHaximus\PhuninNode\PluginInterface',
            [
                'getSlug',
                'getConfiguration',
                'setNode',
                'getValues',
            ]
        );
        $plugin->method('getSlug')
            ->willReturn('a');
        $plugin->method('getConfiguration')
            ->will(
                $this->returnCallback(
                    function ($resolver) {
                        $configuration = new \WyriHaximus\PhuninNode\PluginConfiguration();
                        $configuration->setPair('graph_category', 'a');
                        $resolver->resolve($configuration);
                    }
                )
            );
        $this->node->method('getPlugins')
            ->willReturn($this->plugins);
        $this->plugins->attach($plugin);
    }

    public function tearDown()
    {
        unset($this->loop, $this->socket, $this->node, $this->plugins);
    }

    public function testPlugin()
    {
        $classImplements = class_implements($this->plugin);
        $this->assertTrue(isset($classImplements['WyriHaximus\PhuninNode\PluginInterface']));
    }

    public function testGetConfiguration()
    {

        $callbackRan = false;
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(
            function ($configuration) use (&$callbackRan) {
                $callbackRan = true;
                $this->assertInstanceOf('WyriHaximus\PhuninNode\PluginConfiguration', $configuration);
            }
        );
        $this->plugin->getConfiguration($deferred->resolver());
        $this->assertTrue($callbackRan);

        $callbackRan = false;
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(
            function ($configuration) use (&$callbackRan) {
                $callbackRan = true;
                $this->assertInstanceOf('WyriHaximus\PhuninNode\PluginConfiguration', $configuration);
            }
        );
        $this->plugin->getConfiguration($deferred->resolver());
        $this->assertTrue($callbackRan);
    }

    public function testGetConfigurationValues()
    {

        $callbackRan = false;
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(
            function ($configuration) use (&$callbackRan) {
                $callbackRan = true;
                foreach ($configuration as $value) {
                    $this->assertInstanceOf('WyriHaximus\PhuninNode\Value', $value);
                }
            }
        );
        $this->plugin->getConfiguration($deferred->resolver());
        $this->assertTrue($callbackRan);
    }

    public function testGetValues()
    {

        $callbackRan = false;
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(
            function ($values) use (&$callbackRan) {
                $callbackRan = true;
                $this->assertInstanceOf('SplObjectStorage', $values);
            }
        );
        $this->plugin->getValues($deferred->resolver());
        $this->assertTrue($callbackRan);

        $callbackRan = false;
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(
            function ($values) use (&$callbackRan) {
                $callbackRan = true;
                $this->assertInstanceOf('WyriHaximus\PhuninNode\PluginConfiguration', $values);
            }
        );
        $this->plugin->getValues($deferred->resolver());
        $this->assertTrue($callbackRan);
    }

    public function testGetValuesValues()
    {

        $callbackRan = false;
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(
            function ($values) use (&$callbackRan) {
                $callbackRan = true;
                foreach ($values as $value) {
                    $this->assertInstanceOf('WyriHaximus\PhuninNode\Value', $value);
                }
            }
        );
        $this->plugin->getValues($deferred->resolver());
        $this->assertTrue($callbackRan);
    }

}