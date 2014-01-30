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

use \React\EventLoop\LoopInterface;
use \React\Socket\Server as Socket;
use \React\Socket\Connection;

/**
 * Class Node
 * @package WyriHaximus\PhuninNode
 */
class Node
{

    /**
     * Current version of PhuninNode
     */
    const VERSION = '0.3.0-DEV';

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    /**
     * @var \React\Socket\Server
     */
    private $socket;

    /**
     * Collection of Plugins in use (PluginInterface)
     *
     * @var \SplObjectStorage
     */
    private $plugins;

    /**
     * Collection of connected clients (ConnectionContext)
     *
     * @var \SplObjectStorage
     */
    private $connections;

    /**
     * @param \React\EventLoop\LoopInterface $loop
     * @param \React\Socket\Server $socket The socket to bind on
     */
    public function __construct(LoopInterface $loop, Socket $socket)
    {
        $this->loop = $loop;
        $this->socket = $socket;

        $this->plugins = new \SplObjectStorage;
        $this->connections = new \SplObjectStorage;

        $this->socket->on('connection', [$this, 'onConnection']);
    }

    /**
     * Shutdown Node and the underlying socket
     */
    public function shutdown()
    {
        $this->socket->shutdown();
    }

    /**
     * @param Connection $conn
     */
    public function onConnection(Connection $conn)
    {
        $this->connections->attach(new ConnectionContext($conn, $this));
    }

    /**
     * Detach connection from connection collection
     *
     * @param ConnectionContext $connection
     */
    public function onClose(ConnectionContext $connection)
    {
        $this->connections->detach($connection);
    }

    /**
     * Attach a plugin
     *
     * @param PluginInterface $plugin
     */
    public function addPlugin(PluginInterface $plugin)
    {
        $plugin->setNode($this);

        $this->plugins->attach($plugin);
    }

    /**
     * Returns the plugins collection
     *
     * @return \SplObjectStorage
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Return the connection collection
     *
     * @return \SplObjectStorage
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * return the react
     *
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Get a plugin by slug or return false when none can be found
     *
     * @param string $slug
     * @return bool|PluginInterface
     */
    public function getPlugin($slug)
    {
        $this->plugins->rewind();
        while ($this->plugins->valid()) {
            if ($this->plugins->current()->getSlug() == $slug) {
                return $this->plugins->current();
            }
            $this->plugins->next();
        }

        return false;
    }

    /**
     * Create and setup a promise
     *
     * @param $callback
     * @return \React\Promise\Deferred
     */
    public function resolverFactory($callback)
    {
        $resolver = new \React\Promise\Deferred();
        $resolver->promise()->then($callback);
        return $resolver;
    }
}
