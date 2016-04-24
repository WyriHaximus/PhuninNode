<?php
declare(strict_types=1);

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Socket\Connection;
use React\Socket\Server as Socket;

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
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
     * @var array
     */
    private $defaultConfiguration = [
        'hostname' => 'HOSTNAME',
        'verbose' => false,
    ];

    /**
     * @param LoopInterface $loop
     * @param Socket $socket The socket to bind on
     * @param Configuration $configuration Node configuration
     * @param LoggerInterface $logger Logger
     */
    public function __construct(
        LoopInterface $loop,
        Socket $socket,
        Configuration $configuration = null,
        LoggerInterface $logger = null
    ) {

        if (false === strpos(PHP_VERSION, 'hiphop')) {
            gc_enable();
        }

        $this->loop = $loop;
        $this->socket = $socket;

        if ($configuration === null) {
            $configuration = new Configuration();
        }
        $configuration->applyDefaults($this->defaultConfiguration);
        $this->configuration = $configuration;

        if ($logger === null) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;

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
    public function getPlugins(): \SplObjectStorage
    {
        return $this->plugins;
    }

    /**
     * Return the connection collection
     *
     * @return \SplObjectStorage
     */
    public function getConnections(): \SplObjectStorage
    {
        return $this->connections;
    }

    /**
     * return the react
     *
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
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
}
