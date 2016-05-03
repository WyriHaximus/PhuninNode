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
use function React\Promise\resolve;
use WyriHaximus\PhuninNode\Commands\Config;
use WyriHaximus\PhuninNode\Configuration;
use WyriHaximus\PhuninNode\ConnectionContext;
use function Clue\React\Block\await;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $configuration = new Configuration();
        $configuration->setPair('key', 'value');
        $plugin = Phake::mock(PluginInterface::class);
        Phake::when($plugin)->getConfiguration()->thenReturn(resolve($configuration));
        $node = Phake::mock(Node::class);
        Phake::when($node)->getPlugin('plugin')->thenReturn($plugin);
        $context = Phake::mock(ConnectionContext::class);
        $config = new Config();
        $config->setNode($node);
        $this->assertSame(
            [
                'key value',
                '.',
            ],
            await(
                $config->handle(
                    $context,
                    'plugin'
                ),
                Factory::create()
            )
        );
    }

    public function testHandleNoPlugin()
    {
        $node = Phake::mock(Node::class);
        Phake::when($node)->getPlugin('no_plugin')->thenReturn(false);
        $context = Phake::mock(ConnectionContext::class);
        $config = new Config();
        $config->setNode($node);
        $this->assertSame(
            null,
            await(
                $config->handle(
                    $context,
                    'no_plugin'
                ),
                Factory::create()
            )
        );
        Phake::verify($context)->quit();
    }

    public function testHandleNoInput()
    {
        $context = Phake::mock(ConnectionContext::class);
        $config = new Config();
        $this->assertSame(
            null,
            await(
                $config->handle(
                    $context,
                    ''
                ),
                Factory::create()
            )
        );
        Phake::verify($context)->quit();
    }
}
