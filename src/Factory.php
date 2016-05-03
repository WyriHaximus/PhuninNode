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

namespace WyriHaximus\PhuninNode;

use React\EventLoop\LoopInterface;
use React\Socket\Server;
use WyriHaximus\PhuninNode\Commands;

class Factory
{
    public static function create(
        LoopInterface $loop,
        string $ip,
        int $port,
        Configuration $configuration = null
    ): Node {
        $socket = new Server($loop);
        $socket->listen($port, $ip);

        return new Node(
            $loop,
            $socket,
            self::createCommands(),
            $configuration
        );
    }

    public static function createCommands(): CommandsCollection
    {
        return new CommandsCollection([
            'cap' => new Commands\Cap(),
            'config' => new Commands\Config(),
            'fetch' => new Commands\Fetch(),
            'list' => new Commands\Lst(),
            'nodes' => new Commands\Nodes(),
            'quit' => new Commands\Quit(),
            'version' => new Commands\Version(),
        ]);
    }
}
