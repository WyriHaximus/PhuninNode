<?php

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
use SplObjectStorage;
use WyriHaximus\PhuninNode\Commands\Lst;
use WyriHaximus\PhuninNode\ConnectionContext;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;
use function Clue\React\Block\await;

class LstTest extends \PHPUnit_Framework_TestCase
{

    public function testHandle()
    {
        $pluginA = Phake::mock(PluginInterface::class);
        Phake::when($pluginA)->getSlug()->thenReturn('plugin_a');
        $pluginB = Phake::mock(PluginInterface::class);
        Phake::when($pluginB)->getSlug()->thenReturn('plugin_b');
        $plugins = new SplObjectStorage();
        $plugins->attach($pluginA);
        $plugins->attach($pluginB);
        $node = Phake::mock(Node::class);
        Phake::when($node)->getPlugins()->thenReturn($plugins);
        $list = new Lst();
        $list->setNode($node);
        $this->assertSame(
            [
                'plugin_a plugin_b',
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
