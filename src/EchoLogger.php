<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode;

use Psr\Log\AbstractLogger;

/**
 * Class EchoLogger
 * @package WyriHaximus\PhuninNode
 */
class EchoLogger extends AbstractLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        echo '[', $level, '] ', $message, PHP_EOL;
    }
}
