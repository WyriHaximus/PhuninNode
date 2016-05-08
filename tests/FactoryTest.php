<?php
declare(strict_types=1);

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
use React\EventLoop\LoopInterface;
use WyriHaximus\PhuninNode\Commands\CommandInterface;
use WyriHaximus\PhuninNode\CommandsCollection;
use WyriHaximus\PhuninNode\Factory;
use WyriHaximus\PhuninNode\Node;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(
            Node::class,
            Factory::create(
                Phake::mock(LoopInterface::class),
                '0.0.0.0',
                12345
            )
        );
    }

    public function testCreateCommands()
    {
        $commands = Factory::createCommands();
        $this->assertInstanceOf(CommandsCollection::class, $commands);
        $this->assertTrue(count($commands) > 0);
        foreach ($commands->keys() as $command) {
            $this->assertInstanceOf(CommandInterface::class, $commands->get($command));
        }
    }
}
