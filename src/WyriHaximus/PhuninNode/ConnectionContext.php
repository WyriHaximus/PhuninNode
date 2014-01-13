<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode;

/**
 * Class ConnectionContext
 * @package WyriHaximus\PhuninNode
 */
class ConnectionContext
{
    /**
     * The greeting munin expects
     */
    const GREETING = "# munin node at HOSTNAME\n";

    /**
     * @var \React\Socket\Connection
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
     * @param \React\Socket\Connection $conn
     * @param Node $node
     */
    public function __construct(\React\Socket\Connection $conn, Node $node)
    {
        $this->conn = $conn;
        $this->node = $node;

        $this->conn->on('data', [$this, 'onData']);
        $this->conn->on('close', [$this, 'onClose']);

        $this->commandMap['list'] = [$this, 'onList'];
        $this->commandMap['nodes'] = [$this, 'onNodes'];
        $this->commandMap['version'] = [$this, 'onVersion'];
        $this->commandMap['config'] = [$this, 'onConfig'];
        $this->commandMap['fetch'] = [$this, 'onFetch'];
        $this->commandMap['quit'] = [$this, 'onQuit'];

        $this->conn->write(self::GREETING);
    }

    /**
     * Handle a command call from the clients side
     *
     * @param string $data
     */
    public function onData($data)
    {
        $data = trim($data);
        list($command) = explode(' ', $data);
        if (isset($this->commandMap[$command])) {
            call_user_func_array($this->commandMap[$command], [$data]);
        } else {
            $list = implode(', ', array_keys($this->commandMap));
            $this->conn->write(
                '# Unknown command. Try ' . substr_replace($list, ' or ', strrpos($list, ', '), 2) . "\n"
            );
        }
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
        $this->conn->write(implode(' ', $list) . "\n");
    }

    /**
     * List all connected nodes (for now only localhost)
     */
    public function onNodes()
    {
        $this->conn->write(implode(' ', ['HOSTNAME']) . "\n");
    }

    /**
     * Respond with the current version
     */
    public function onVersion()
    {
        $this->conn->write('PhuninNode on HOSTNAME version: ' . Node::VERSION . "\n");
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
                    $this->conn->write($pair->getKey() . ' ' . $pair->getValue() . "\n");
                }
                $this->conn->write(".\n");
            }
        );
        $plugin->getConfiguration($deferred->resolver());
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
                        $this->conn->write(
                            $value->getKey() . '.value ' . str_replace(',', '.', $value->getValue()) . "\n"
                        );
                    }
                    $this->conn->write(".\n");
                }
            );
            $plugin->getValues($deferred->resolver());
        } else {
            $this->conn->close();
        }
    }

    /**
     * Close connection
     */
    public function onQuit()
    {
        $this->conn->close();
    }

    /**
     * Close connection
     */
    public function onClose()
    {
        $this->node->onClose($this);
    }
}
