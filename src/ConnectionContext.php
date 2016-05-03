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

use \React\EventLoop\Timer\TimerInterface;
use React\EventLoop\LoopInterface;
use React\Stream\DuplexStreamInterface;

/**
 * Class ConnectionContext
 * @package WyriHaximus\PhuninNode
 */
class ConnectionContext
{
    /**
     * The greeting munin expects
     */
    const GREETING = '# munin node at %s';

    /**
     * The timeout after which we disconnection for no data transmission
     */
    const CONNECTION_TIMEOUT = 60;

    /**
     * New line
     */
    const NEW_LINE = "\n";

    /**
     * @var DuplexStreamInterface
     */
    private $conn;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var TimerInterface
     */
    private $timeoutTimer;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @param DuplexStreamInterface $conn
     * @param Node $node
     */
    public function __construct(DuplexStreamInterface $conn, Node $node)
    {
        $this->conn = $conn;
        $this->node = $node;
        $this->loop = $node->getLoop();

        $this->conn->on('data', [$this, 'onData']);
        $this->conn->on('close', [$this, 'onClose']);

        $this->write(
            sprintf(
                self::GREETING,
                $node->getConfiguration()
                    ->getPair('hostname')
                    ->getValue()
            )
        );
    }

    /**
     * Write data to the connection
     *
     * @param string $data
     */
    protected function write(string $data)
    {
        $this->clearTimeout();
        $this->conn->write($data . static::NEW_LINE);
        $this->log('<-' . $data);
        $this->setTimeout();
    }

    /**
     * Clear the timeout, close the connection, and tell node the client disconnected
     */
    protected function close()
    {
        $this->clearTimeout();
        $this->node->onClose($this);
    }

    /**
     * Set a timeout that disconnects the client when it's idle for to long
     */
    protected function setTimeout()
    {
        $this->timeoutTimer = $this->loop->addTimer(
            self::CONNECTION_TIMEOUT,
            function () {
                $this->close();
            }
        );
    }

    /**
     * Clear the timeout if it's set
     */
    protected function clearTimeout()
    {
        if ($this->timeoutTimer !== null) {
            $this->loop->cancelTimer($this->timeoutTimer);
            $this->timeoutTimer = null;
        }
    }

    /**
     * Handle a command call from the clients side
     *
     * @param string $data
     */
    public function onData(string $data)
    {
        $data = trim($data);
        $this->log('->' . $data);
        $chunks = explode(' ', $data);
        $command = $chunks[0];
        unset($chunks[0]);
        $input = '';
        if (count($chunks) > 0) {
            $input = implode(' ', $chunks);
        }
        $input = trim($input);
        if ($this->node->getCommands()->has($command)) {
            $this
                ->node
                ->getCommands()
                ->get($command)
                ->handle($this, $input)
                ->then(function (array $lines) {
                    foreach ($lines as $line) {
                        $this->write($line);
                    }
                });
            return;
        }

        $list = implode(', ', $this->node->getCommands()->keys());
        $this->write(
            '# Unknown command. Try ' . substr_replace($list, ' or ', strrpos($list, ', '), 2)
        );
    }

    /**
     * Close connection
     */
    public function quit()
    {
        $this->conn->close();
        $this->close();
    }

    /**
     * Close connection
     */
    public function onClose()
    {
        $this->close();
    }

    /**
     * @param string $message
     */
    protected function log(string $message)
    {
        $this->node->getLogger()->debug('[' . spl_object_hash($this->conn) . ']' . $message);
    }
}
