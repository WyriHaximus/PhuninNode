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

namespace WyriHaximus\PhuninNode\Commands;

use React\Promise\PromiseInterface;
use WyriHaximus\PhuninNode\ConnectionContext;
use WyriHaximus\PhuninNode\Node;

interface CommandInterface
{
    public function setNode(Node $node);
    public function handle(ConnectionContext $context, string $line): PromiseInterface;
}
