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
use WyriHaximus\PhuninNode\Commands\Quit;
use WyriHaximus\PhuninNode\ConnectionContext;
use function Clue\React\Block\await;

class QuitTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $context = Phake::mock(ConnectionContext::class);
        $list = new Quit();
        $this->assertSame(
            [],
            await(
                $list->handle(
                    $context,
                    ''
                ),
                Factory::create()
            )
        );
        Phake::verify($context)->quit();
    }
}
