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

    private $loop;
    private $socket;
    
    private $plugins;
    private $connections;
    
    public function __construct($loop, $socket) {
        $this->loop = $loop;
		$this->socket = $socket;
            
        $this->plugins = new \SplObjectStorage;
        $this->connections = new \SplObjectStorage;
        
        $this->socket->on('connection', [$this, 'onConnection']);
    }
    
    public function shutdown() {
        $this->socket->shutdown();
    }
    
    public function onConnection(\React\Socket\Connection $conn) {
        $this->connections->attach(new ConnectionContext($conn, $this));
    }
    
    public function onClose($connection) {
        $this->connections->detach($connection);
    }
    
    public function addPlugin(\WyriHaximus\PhuninNode\Interfaces\Plugin $plugin) {
        $plugin->setNode($this);
        
        $this->plugins->attach($plugin);
    }
    
    public function getPlugins() {
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