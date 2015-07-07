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

/**
 * Class ConnectionContextTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class ConnectionContextTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->loop = $this->getMock(
            '\React\EventLoop\StreamSelectLoop',
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
            ->willReturn($this->getMock('\React\EventLoop\Timer\TimerInterface'));

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
            '\WyriHaximus\PhuninNode\Interfaces\Plugin',
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
        $this->plugins->attach($plugin);
    }

    public function tearDown()
    {
        unset($this->loop, $this->socket, $this->node, $this->plugins);
    }

    public function testConstruct()
    {
        $connection = $this->getMock(
            '\React\Socket\Connection',
            [
                'on',
                'write',
            ],
            [
                $this->socket,
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
                            '\WyriHaximus\PhuninNode\ConnectionContext',
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
                            '\WyriHaximus\PhuninNode\ConnectionContext',
                            $callback[0]
                        ) && $this->identicalTo('onClose', $callback[1]);
                    }
                )
            );
        $connection->expects($this->at(2))
            ->method('write')
            ->with("# munin node at HOSTNAME\n");

        $connetionContext = new \WyriHaximus\PhuninNode\ConnectionContext($connection, $this->node);
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
                        $values->attach(new \WyriHaximus\PhuninNode\Value(1, 2));
                        $deferredResolver->resolve($values);
                    }
                )
            );

        $connection = $this->getMock(
            '\React\Socket\Connection',
            [
                'write',
                'close',
            ],
            [
                $this->socket,
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
                        $resolver = new \React\Promise\Deferred();
                        $resolver->promise()->then($callback);
                        return $resolver;
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


        $connetionContext = new \WyriHaximus\PhuninNode\ConnectionContext($connection, $this->node);

        $connetionContext->onData("list\n");
        $connetionContext->onData("nodes\n");
        $connetionContext->onData("version\n");
        $connetionContext->onData("config\n");
        $connetionContext->onData("config b\n");
        $connetionContext->onData("config a\n");
        $connetionContext->onData("fetch\n");
        $connetionContext->onData("fetch b\n");
        $connetionContext->onData("fetch a\n");
        $connetionContext->onData("quit\n");
        $connetionContext->onData("skjargyefw\n");
    }
}
