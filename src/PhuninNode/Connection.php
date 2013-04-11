<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhuninNode;

class Connection {
    private $conn;
    private $node;
    private $commandMap = array();
    public function __construct($conn, $node) {
        $this->conn = $conn;
        $this->node = $node;
        $this->conn->write("# munin node at HOSTNAME\n");
        
        $that = $this;
        $this->conn->on('data', function($data) use ($that) {
            $that->onData($data);
        });
        $this->conn->on('close', function($data) use ($that) {
            $that->onClose($data);
        });
        
        $this->commandMap['list'] = array($this, 'onList');
        $this->commandMap['nodes'] = array($this, 'onNodes');
        $this->commandMap['version'] = array($this, 'onVersion');
        $this->commandMap['config'] = array($this, 'onConfig');
        $this->commandMap['fetch'] = array($this, 'onFetch');
        $this->commandMap['quit'] = array($this, 'onQuit');
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
        $list = array();
        foreach ($this->node->getPlugins() as $plugin) {
            $list[] = $plugin->getSlug();
        }
        $this->conn->write(implode(' ', $list) . "\n");
    }
    
    private function onNodes($data) {
        $this->conn->write(implode(' ', array('HOSTNAME')) . "\n");
    }
    
    private function onVersion($data) {
        $this->conn->write('PhuninNode on HOSTNAME version: ' . Node::VERSION . "\n");
    }
    
    private function onConfig($data) {
        list(, $resource) = explode(' ', $data);
        $plugin = $this->node->getPlugin($resource);
        if ($plugin !== false) {
            foreach ($plugin->getConfiguration()->getPairs() as $configuration) {
                $this->conn->write($configuration->getKey() . ' ' . $configuration->getValue() . "\n");
            }
            $this->conn->write(".\n");
        } else {
            $this->conn->close();
        }
    }
    
    private function onFetch($data) {
        list(, $resource) = explode(' ', $data);
        $plugin = $this->node->getPlugin($resource);
        if ($plugin !== false) {
            foreach ($plugin->getValues() as $value) {
                $this->conn->write($value->getKey() . '.value ' . str_replace(',', '.', $value->getValue()) . "\n");
            }
            $this->conn->write(".\n");
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