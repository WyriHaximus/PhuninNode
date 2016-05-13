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

namespace WyriHaximus\PhuninNode\Tests\Commands;

use Phake;
use React\EventLoop\Factory;
use WyriHaximus\PhuninNode\Commands\Cap;
use WyriHaximus\PhuninNode\ConnectionContext;
use WyriHaximus\PhuninNode\Node;
use function Clue\React\Block\await;
use WyriHaximus\PhuninNode\PluginInterface;

class CapTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $node = Phake::mock(Node::class);
        $list = new Cap();
        $list->setNode($node);
        $this->assertSame(
            [],
            await(
                $list->handle(
                    Phake::mock(ConnectionContext::class),
                    ''
                ),
                Factory::create()
            )
        );
    }

    public function testHandleCapabilities()
    {
        $pluginA = Phake::mock(PluginInterface::class);
        Phake::when($pluginA)->getCapabilities()->thenReturn([
            'foo',
            'bar',
        ]);
        $pluginB = Phake::mock(PluginInterface::class);
        Phake::when($pluginB)->getCapabilities()->thenReturn([
            'bar',
            'beer',
            'multigraph',
            'whiskey',
        ]);
        $plugins = new \SplObjectStorage();
        $plugins->attach($pluginA);
        $plugins->attach($pluginB);
        $node = Phake::mock(Node::class);
        Phake::when($node)->getPlugins()->thenReturn($plugins);
        $list = new Cap();
        $list->setNode($node);
        $this->assertSame(
            [
                'foo',
                'bar',
                'beer',
                'multigraph',
                'whiskey',
            ],
            await(
                $list->handle(
                    Phake::mock(ConnectionContext::class),
                    ''
                ),
                Factory::create()
            )
        );
    }
}
