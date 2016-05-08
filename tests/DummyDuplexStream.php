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

namespace WyriHaximus\PhuninNode\Tests;

use Evenement\EventEmitterTrait;
use React\Stream\DuplexStreamInterface;
use React\Stream\WritableStreamInterface;

class DummyDuplexStream implements DuplexStreamInterface
{
    use EventEmitterTrait;

    public function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    public function pause()
    {
        // TODO: Implement pause() method.
    }

    public function resume()
    {
        // TODO: Implement resume() method.
    }

    public function pipe(WritableStreamInterface $dest, array $options = array())
    {
        // TODO: Implement pipe() method.
    }

    public function close()
    {
        // TODO: Implement close() method.
    }

    public function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    public function write($data)
    {
        // TODO: Implement write() method.
    }

    public function end($data = null)
    {
        // TODO: Implement end() method.
    }
}
