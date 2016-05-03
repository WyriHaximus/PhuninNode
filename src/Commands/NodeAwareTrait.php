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

use WyriHaximus\PhuninNode\ConnectionContext;
use WyriHaximus\PhuninNode\Node;
use function React\Promise\resolve;

trait NodeAwareTrait
{
    /**
     * @var Node
     */
    private $node;

    /**
     * @param Node $node
     */
    public function setNode(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return Node
     */
    protected function getNode(): Node
    {
        return $this->node;
    }
}
