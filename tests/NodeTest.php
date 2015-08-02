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

use React\EventLoop\StreamSelectLoop;
use React\Promise\Deferred;
use React\Socket\Connection;
use WyriHaximus\PhuninNode\ConnectionContext;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\Configuration;
use WyriHaximus\PhuninNode\PluginInterface;

/**
 * Class NodeTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->loop = $this->getMock(StreamSelectLoop::class);
        $this->socket = $this->getMock(
            '\React\Socket\Server',
            [
                'on',
                'write',
                'shutdown',
            ],
            [
                $this->loop,
                '0.0.0.0',
                12345
            ]
        );

        $this->plugins = new \SplObjectStorage();
        $plugin = $this->getMock(
            PluginInterface::class,
            [
                'getSlug',
                'getCategorySlug',
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
                    function (Deferred $resolver) {
                        $configuration = new Configuration();
                        $configuration->setPair('graph_category', 'a');
                        $resolver->resolve($configuration);
                    }
                )
            );
        $this->plugins->attach($plugin);
    }

    public function tearDown()
    {
        unset($this->loop, $this->socket, $this->plugins);
    }

    public function testConstruct()
    {
        $this->socket->expects($this->once())
            ->method('on')
            ->with(
                'connection',
                $this->callback(
                    function ($callback) {
                        return $this->isInstanceOf(Node::class, $callback[0]) && $this->identicalTo(
                            'onConnection',
                            $callback[1]
                        );
                    }
                )
            );
        new Node($this->loop, $this->socket);
    }

    public function testShutdown()
    {
        $this->socket->expects($this->once())
            ->method('shutdown');
        $node = new Node($this->loop, $this->socket);
        $node->shutdown();
    }

    public function testOnConnection()
    {
        $connection = $this->getMock(
            Connection::class,
            [],
            [
                fopen('php://temp', 'r+'),
                $this->loop,
            ]
        );
        $node = new Node($this->loop, $this->socket);
        $node->onConnection($connection);
        $connections = $node->getConnections();
        $connections->rewind();
        $this->assertSameSize(['a'], $connections);
        $this->assertInstanceOf(ConnectionContext::class, $connections->current());
    }

    public function testOnClose()
    {
        $connection = $this->getMock(
            Connection::class,
            [],
            [
                fopen('php://temp', 'r+'),
                $this->loop,
            ]
        );
        $node = new Node($this->loop, $this->socket);
        $node->onConnection($connection);
        $connections = $node->getConnections();
        $connections->rewind();
        $node->onClose($connections->current());
        $this->assertSameSize([], $node->getConnections());
    }

    public function testAddPlugin()
    {
        $node = new Node($this->loop, $this->socket);
        $this->assertSameSize([], $node->getPlugins());
        $this->plugins->rewind();
        $node->addPlugin($this->plugins->current());
        $this->assertSameSize(['a'], $node->getPlugins());
    }

    public function testGetPluginsEmpty()
    {
        $node = new Node($this->loop, $this->socket);
        $this->assertSameSize([], $node->getPlugins());
    }

    public function testGetPluginsOne()
    {
        $node = new Node($this->loop, $this->socket);
        $this->plugins->rewind();
        $node->addPlugin($this->plugins->current());
        $this->assertSameSize(['a'], $node->getPlugins());
    }

    public function testGetConnectionsEmpty()
    {
        $node = new Node($this->loop, $this->socket);
        $this->assertSameSize([], $node->getConnections());
    }

    public function testGetConnectionsOne()
    {
        $connection = $this->getMock(
            Connection::class,
            [],
            [
                fopen('php://temp', 'r+'),
                $this->loop,
            ]
        );
        $node = new Node($this->loop, $this->socket);
        $node->onConnection($connection);
        $this->assertSameSize(['a'], $node->getConnections());
    }

    public function testGetPlugin()
    {
        $node = new Node($this->loop, $this->socket);
        $this->plugins->rewind();
        $node->addPlugin($this->plugins->current());
        $this->assertSame($this->plugins->current(), $node->getPlugin('a'));
        $this->assertTrue(!$node->getPlugin('b'));
    }

    public function testGetLoop()
    {
        $node = new Node($this->loop, $this->socket);
        $this->assertSame($this->loop, $node->getLoop());
    }
}
