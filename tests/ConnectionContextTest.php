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
use React\EventLoop\Timer\TimerInterface;
use React\Promise\Deferred;
use React\Socket\Connection;
use React\Socket\Server;
use WyriHaximus\PhuninNode\ConnectionContext;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\Configuration;
use WyriHaximus\PhuninNode\Plugins\Plugins;
use WyriHaximus\PhuninNode\Value;

/**
 * Class ConnectionContextTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class ConnectionContextTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->loop = $this->getMock(
            StreamSelectLoop::class,
            [
                'addReadStream',
                'addWriteStream',
                'removeReadStream',
                'removeWriteStream',
                'removeStream',
                'addTimer',
                'cancelTimer',
            ]
        );
        $this->loop->expects($this->atleastOnce())
            ->method('addTimer')
            ->willReturn($this->getMock(TimerInterface::class));

        $this->socket = $this->getMock(
            Server::class,
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
            Node::class,
            [
                'getPlugins',
                'getPlugin',
                'getValues',
                'getLoop',
            ],
            [
                $this->loop,
                $this->socket,
            ]
        );
        $this->node->expects($this->once())
            ->method('getLoop')
            ->willReturn($this->loop);

        $this->plugins = new \SplObjectStorage();
        $plugin = $this->getMock(
            Plugins::class,
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
                    function (Deferred $deferred) {
                        $configuration = new Configuration();
                        $configuration->setPair('graph_category', 'a');
                        $deferred->resolve($configuration);
                    }
                )
            );
        $this->plugins->attach($plugin);
    }

    public function tearDown()
    {
        unset($this->loop, $this->socket, $this->node, $this->plugins);
    }

    public function testConstruct()
    {
        $connection = $this->getMock(
            Connection::class,
            [
                'on',
                'write',
            ],
            [
                fopen('php://temp', 'r+'),
                $this->loop,
            ]
        );

        $connection->expects($this->at(0))
            ->method('on')
            ->with(
                'data',
                $this->callback(
                    function ($callback) {
                        return $this->isInstanceOf(
                            ConnectionContext::class,
                            $callback[0]
                        ) && $this->identicalTo('onData', $callback[1]);
                    }
                )
            );
        $connection->expects($this->at(1))
            ->method('on')
            ->with(
                'close',
                $this->callback(
                    function ($callback) {
                        return $this->isInstanceOf(
                            ConnectionContext::class,
                            $callback[0]
                        ) && $this->identicalTo('onClose', $callback[1]);
                    }
                )
            );
        $connection->expects($this->at(2))
            ->method('write')
            ->with("# munin node at HOSTNAME\n");

        new ConnectionContext($connection, $this->node);
    }

    public function testOnData()
    {
        $this->loop->expects($this->atleastOnce())
            ->method('cancelTimer');

        $this->node->expects($this->any())
            ->method('getPlugins')
            ->willReturn($this->plugins);
        $this->plugins->rewind();
        $this->node->expects($this->any())
            ->method('getPlugin')
            ->willReturn($this->plugins->current());
        $this->plugins->current()
            ->method('getValues')
            ->will(
                $this->returnCallback(
                    function ($deferredResolver) {
                        $values = new \SplObjectStorage;
                        $values->attach(new Value(1, 2));
                        $deferredResolver->resolve($values);
                    }
                )
            );

        $connection = $this->getMock(
            Connection::class,
            [
                'write',
                'close',
            ],
            [
                fopen('php://temp', 'r+'),
                $this->loop,
            ]
        );

        $connection->expects($this->at(1))
            ->method('write')
            ->with("a\n");
        $connection->expects($this->at(2))
            ->method('write')
            ->with("HOSTNAME\n");
        $connection->expects($this->at(3))
            ->method('write')
            ->with("PhuninNode on HOSTNAME version: 0.3.0-DEV\n");
        $connection->expects($this->at(4))
            ->method('close');
        $connection->expects($this->at(5))
            ->method('close');

        $this->node->expects($this->any())
            ->method('resolverFactory')
            ->will(
                $this->returnCallback(
                    function ($callback) {
                        $deferred = new Deferred();
                        $deferred->promise()->then($callback);
                        return $deferred;
                    }
                )
            );

        $connection->expects($this->at(6))
            ->method('write')
            ->with(".\n");
        $connection->expects($this->at(7))
            ->method('write')
            ->with("graph_category a\n");
        $connection->expects($this->at(8))
            ->method('write')
            ->with(".\n");
        $connection->expects($this->at(9))
            ->method('close');
        $connection->expects($this->at(10))
            ->method('close');
        $connection->expects($this->at(11))
            ->method('write')
            ->with(".\n");
        $connection->expects($this->at(12))
            ->method('write')
            ->with("1.value 2\n");
        $connection->expects($this->at(13))
            ->method('write')
            ->with(".\n");
        $connection->expects($this->at(14))
            ->method('close');
        $connection->expects($this->at(15))
            ->method('write')
            ->with("# Unknown command. Try cap, list, nodes, version, config, fetch or quit\n");


        $connectionContext = new ConnectionContext($connection, $this->node);

        $connectionContext->onData("list\n");
        $connectionContext->onData("nodes\n");
        $connectionContext->onData("version\n");
        $connectionContext->onData("config\n");
        $connectionContext->onData("config b\n");
        $connectionContext->onData("config a\n");
        $connectionContext->onData("fetch\n");
        $connectionContext->onData("fetch b\n");
        $connectionContext->onData("fetch a\n");
        $connectionContext->onData("quit\n");
        $connectionContext->onData("skjargyefw\n");
    }
}
