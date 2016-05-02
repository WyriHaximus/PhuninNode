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

use Phake;
use WyriHaximus\PhuninNode\Commands\CommandInterface;
use WyriHaximus\PhuninNode\CommandsCollection;
use WyriHaximus\PhuninNode\Node;

class CommandsCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testSetNode()
    {
        $node = Phake::mock(Node::class);
        $command = Phake::mock(CommandInterface::class);
        $collection = new CommandsCollection([$command]);
        $collection->setNode($node);
        Phake::verify($command)->setNode($node);
    }

    public function testHas()
    {
        $command = Phake::mock(CommandInterface::class);
        $collection = new CommandsCollection(['command' => $command]);
        $this->assertTrue($collection->has('command'));
        $this->assertFalse($collection->has('commando'));
    }

    public function testGet()
    {
        $command = Phake::mock(CommandInterface::class);
        $collection = new CommandsCollection(['command' => $command]);
        $this->assertSame($command, $collection->get('command'));
    }

    /**
     * @expectedException Exception
     */
    public function testGetFail()
    {
        $collection = new CommandsCollection([]);
        $collection->get('commando');
    }

    public function testKeys()
    {
        $command = Phake::mock(CommandInterface::class);
        $collection = new CommandsCollection(['command' => $command]);
        $this->assertSame([
            'command',
        ], $collection->keys());
    }
}
