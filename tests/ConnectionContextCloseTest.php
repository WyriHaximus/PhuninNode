<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests;

use Phake;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use WyriHaximus\PhuninNode\CommandsCollection;
use WyriHaximus\PhuninNode\ConnectionContext;
use WyriHaximus\PhuninNode\Node;

/**
 * Class ConnectionContextTest
 * @package WyriHaximus\PhuninNode\Tests
 */
class ConnectionContextCloseTest extends \PHPUnit_Framework_TestCase
{
    public function testClose()
    {
        $loop = Phake::mock(LoopInterface::class);
        $connection = new DummyDuplexStream();
        $node = Phake::mock(Node::class);
        Phake::when($node)->getLoop()->thenReturn($loop);
        Phake::when($node)->getLogger()->thenReturn(Phake::mock(LoggerInterface ::class));
        Phake::when($node)->getCommands()->thenReturn(new CommandsCollection([]));

        new ConnectionContext($connection, $node);

        $connection->emit('close');
        Phake::verify($node)->onClose($this->isInstanceOf(ConnectionContext::class));
    }
}
