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
use WyriHaximus\PhuninNode\Commands\Version;
use WyriHaximus\PhuninNode\Configuration;
use WyriHaximus\PhuninNode\ConnectionContext;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\Value;
use function Clue\React\Block\await;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $configuration = Phake::mock(Configuration::class);
        Phake::when($configuration)->getPair('hostname')->thenReturn(new Value('hostname', '::1'));
        $node = Phake::mock(Node::class);
        Phake::when($node)->getConfiguration()->thenReturn($configuration);
        $list = new Version();
        $list->setNode($node);
        $this->assertSame(
            [
                'PhuninNode on ::1 version: 0.3.0-DEV',
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
