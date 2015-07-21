<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode;

use React\EventLoop\LoopInterface;
use \React\EventLoop\Timer\TimerInterface;
use React\Socket\Connection;

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
     * Version message
     */
    const VERSION_MESSAGE = 'PhuninNode on HOSTNAME version: %s';

    /**
     * The timeout after which we disconnection for no data transmission
     */
    const CONNECTION_TIMEOUT = 60;

    /**
     * New line
     */
    const NEW_LINE = "\n";

    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var array
     */
    private $commandMap = [];

    /**
     * @var TimerInterface
     */
    private $timeoutTimer;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @param Connection $conn
     * @param Node $node
     */
    public function __construct(Connection $conn, Node $node)
    {
        $this->conn = $conn;
        $this->node = $node;
        $this->loop = $node->getLoop();

        $this->conn->on('data', [$this, 'onData']);
        $this->conn->on('close', [$this, 'onClose']);

        $this->commandMap['cap'] = [$this, 'onCap'];
        $this->commandMap['list'] = [$this, 'onList'];
        $this->commandMap['nodes'] = [$this, 'onNodes'];
        $this->commandMap['version'] = [$this, 'onVersion'];
        $this->commandMap['config'] = [$this, 'onConfig'];
        $this->commandMap['fetch'] = [$this, 'onFetch'];
        $this->commandMap['quit'] = [$this, 'onQuit'];

        $this->write(sprintf(self::GREETING, $node->getConfiguration()->getPair('hostname')->getValue()));
    }

    /**
     * Write data to the connection
     *
     * @param string $data
     */
    protected function write($data)
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
        $this->conn->close();
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
    public function onData($data)
    {
        $data = trim($data);
        $this->log('->' . $data);
        list($command) = explode(' ', $data);
        if (isset($this->commandMap[$command])) {
            call_user_func_array($this->commandMap[$command], [$data]);
        } else {
            $list = implode(', ', array_keys($this->commandMap));
            $this->write(
                '# Unknown command. Try ' . substr_replace($list, ' or ', strrpos($list, ', '), 2)
            );
        }
    }

    /**
     * List capabilities
     */
    public function onCap()
    {
        $this->write('multigraph');
    }

    /**
     * List all plugins
     */
    public function onList()
    {
        $list = [];
        foreach ($this->node->getPlugins() as $plugin) {
            $list[] = $plugin->getSlug();
        }
        $this->write(implode(' ', $list));
    }

    /**
     * List all connected nodes (for now only localhost)
     */
    public function onNodes()
    {
        $this->write(implode(' ', [
            $this->node->getConfiguration()->getPair('hostname')->getValue()
        ]));
    }

    /**
     * Respond with the current version
     */
    public function onVersion()
    {
        $this->write(sprintf(static::VERSION_MESSAGE, Node::VERSION));
    }

    /**
     * Return the configuration for the given plugin
     *
     * @param string $data
     */
    public function onConfig($data)
    {
        $data = explode(' ', $data);

        if (!isset($data[1])) {
            $this->conn->close();
            return;
        }

        $plugin = $this->node->getPlugin(trim($data[1]));

        if ($plugin === false) {
            $this->conn->close();
            return;
        }

        $deferred = $this->node->resolverFactory(
            function ($configuration) {
                foreach ($configuration->getPairs() as $pair) {
                    $this->write($pair->getKey() . ' ' . $pair->getValue());
                }
                $this->write('.');
            },
            function () {
                $this->write('.');
            }
        );
        $plugin->getConfiguration($deferred);
    }

    /**
     * Fetch data for the given plugin
     *
     * @param string $data
     */
    public function onFetch($data)
    {
        $data = explode(' ', $data);

        if (!isset($data[1])) {
            $this->conn->close();
            return;
        }

        $plugin = $this->node->getPlugin(trim($data[1]));

        if ($plugin === false) {
            $this->conn->close();
            return;
        }

        if ($plugin !== false) {
            $deferred = $this->node->resolverFactory(
                function ($values) {
                    foreach ($values as $value) {
                        $this->write(
                            $value->getKey() . '.value ' . str_replace(',', '.', $value->getValue())
                        );
                    }
                    $this->write('.');
                },
                function () {
                    $this->write('.');
                }
            );
            $plugin->getValues($deferred);
        } else {
            $this->conn->close();
        }
    }

    /**
     * Close connection
     */
    public function onQuit()
    {
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
    protected function log($message)
    {
        $this->node->getLogger()->debug('[' . spl_object_hash($this->conn) . ']' . $message);
    }
}
