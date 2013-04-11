<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhuninNode\Tests;

abstract class AbstractConnectionTest extends \PHPUnit_Framework_TestCase {
    
    protected $loop;
    protected $timer;
    protected $conn;
    protected $node;
    
    public function setUp() {
        parent::setUp();
        $loop = \React\EventLoop\Factory::create();
        $this->loop = $loop;
        $this->timer = $this->loop->addTimer(15, function() use ($loop) {
            $loop->stop();
        });
        $this->node = new \PhuninNode\Node($this->loop, 63168, '127.0.0.1');
        $this->node->addPlugin(new \PhuninNode\Plugins\Plugins());
        $this->node->addPlugin(new \PhuninNode\Plugins\PluginsCategories());
        $this->node->addPlugin(new \PhuninNode\Plugins\MemoryUsage());
        
        $client = stream_socket_client('tcp://127.0.0.1:63168');
        $this->conn = new \React\Socket\Connection($client, $this->loop);
        
        $that = $this;
        $this->conn->on('close', function($conn) use ($loop) {
            $loop->stop();
        });
    }
    
    public function tearDown() {
        $this->node->shutdown();
        unset($this->loop, $this->node, $this->timer, $this->conn);
        parent::tearDown();
    }
    
}