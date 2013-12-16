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

class ConnectionContext {
    private $conn;
    private $node;
    private $commandMap = [];
    public function __construct(\React\Socket\Connection $conn, Node $node) {
        $this->conn = $conn;
        $this->node = $node;
        $this->conn->write("# munin node at HOSTNAME\n");
        
        $this->conn->on('data', function($data) {
            $this->onData($data);
        });
        $this->conn->on('close', function($data) {
            $this->onClose($data);
        });
        
        $this->commandMap['list'] = [$this, 'onList'];
        $this->commandMap['nodes'] = [$this, 'onNodes'];
        $this->commandMap['version'] = [$this, 'onVersion'];
        $this->commandMap['config'] = [$this, 'onConfig'];
        $this->commandMap['fetch'] = [$this, 'onFetch'];
        $this->commandMap['quit'] = [$this, 'onQuit'];
    }
    private function onData($data) {
        $data = trim($data);
        list($command) = explode(' ', $data);
        if (isset($this->commandMap[$command])) {
            call_user_func_array($this->commandMap[$command], array($data));
        } else {
            $list = implode(', ', array_keys($this->commandMap));
            $this->conn->write('# Unknown command. Try ' . substr_replace($list, ' or ', strrpos($list, ', '), 2) . "\n");
        }
    }
    
    private function onList($data) {
        $list = [];
        foreach ($this->node->getPlugins() as $plugin) {
            $list[] = $plugin->getSlug();
        }
        $this->conn->write(implode(' ', $list) . "\n");
    }
    
    private function onNodes($data) {
        $this->conn->write(implode(' ', ['HOSTNAME']) . "\n");
    }
    
    private function onVersion($data) {
        $this->conn->write('PhuninNode on HOSTNAME version: ' . Node::VERSION . "\n");
    }
    
    private function onConfig($data) {
        list(, $resource) = explode(' ', $data);
        $plugin = $this->node->getPlugin($resource);
        if ($plugin !== false) {
            $deferred = new \React\Promise\Deferred();
            $deferred->promise()->then(function($configuration) {
                foreach ($configuration->getPairs() as $pair) {
                    $this->conn->write($pair->getKey() . ' ' . $pair->getValue() . "\n");
                }
                $this->conn->write(".\n");
            });
            $plugin->getConfiguration($deferred->resolver());
        } else {
            $this->conn->close();
        }
    }
    
    private function onFetch($data) {
        list(, $resource) = explode(' ', $data);
        $plugin = $this->node->getPlugin($resource);
        if ($plugin !== false) {
            $deferred = new \React\Promise\Deferred();
            $deferred->promise()->then(function($values) {
                foreach ($values as $value) {
                    $this->conn->write($value->getKey() . '.value ' . str_replace(',', '.', $value->getValue()) . "\n");
                }
                $this->conn->write(".\n");
            });
            $plugin->getValues($deferred->resolver());
        } else {
            $this->conn->close();
        }
    }
    
    private function onQuit($data) {
        $this->conn->close();
    }
    
    private function onClose() {
        $this->node->onClose($this);
    }
}