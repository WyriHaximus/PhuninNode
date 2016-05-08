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
use function React\Promise\resolve;
use WyriHaximus\PhuninNode\Commands\Fetch;
use WyriHaximus\PhuninNode\ConnectionContext;
use function Clue\React\Block\await;
use WyriHaximus\PhuninNode\Metric;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;

class FetchTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $values = new \SplObjectStorage();
        $values->attach(new Metric('key', 1));
        $values->attach(new Metric('value', 1.337));
        $plugin = Phake::mock(PluginInterface::class);
        Phake::when($plugin)->getValues()->thenReturn(resolve($values));
        $node = Phake::mock(Node::class);
        Phake::when($node)->getPlugin('plugin')->thenReturn($plugin);
        $context = Phake::mock(ConnectionContext::class);
        $fetch = new Fetch();
        $fetch->setNode($node);
        $this->assertSame(
            [
                'key.value 1',
                'value.value 1.337',
                '.',
            ],
            await(
                $fetch->handle(
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
        $fetch = new Fetch();
        $fetch->setNode($node);
        $this->assertSame(
            null,
            await(
                $fetch->handle(
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
        $fetch = new Fetch();
        $this->assertSame(
            null,
            await(
                $fetch->handle(
                    $context,
                    ''
                ),
                Factory::create()
            )
        );
        Phake::verify($context)->quit();
    }
}
