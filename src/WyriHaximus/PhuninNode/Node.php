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

class Node {
    
    const VERSION = '0.3.0-DEV';
    
    private $port = 4949;
    private $ip = '0.0.0.0';
    private $loop;
    private $socket;
    
    private $plugins;
    private $connections;
    
    public function __construct($loop, $port = 4949, $ip = '0.0.0.0') {
        $this->loop = $loop;
        $this->port = (int) $port;
        $this->ip = $ip;
            
        $this->plugins = new \SplObjectStorage;
        $this->connections = new \SplObjectStorage;
        
        $this->socket = new \React\Socket\Server($this->loop);
        $this->socket->listen($this->port, $this->ip);
        
        $this->socket->on('connection', function($conn) {
            $this->onConnection($conn);
        });
    }
    
    public function shutdown() {
        $this->socket->shutdown();
    }
    
    public function onConnection($conn) {
        $this->connections->attach(new ConnectionContext($conn, $this));
    }
    
    public function onClose($connection) {
        $this->connections->detach($connection);
    }
    
    public function addPlugin(\WyriHaximus\PhuninNode\Interfaces\Plugin $plugin) {
        $plugin->setNode($this);
        
        $this->plugins->attach($plugin);
    }
    
    public function getPLugins() {
        return $this->plugins;
    }
    
    public function getPlugin($slug) {
        $this->plugins->rewind();
        while($this->plugins->valid()) {
            if ($this->plugins->current()->getSlug() == $slug) {
                return $this->plugins->current();
            }
            $this->plugins->next();
        }
        
        return false;
    }
    
}