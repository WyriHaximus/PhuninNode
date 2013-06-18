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

class ConnectionContextTest extends AbstractConnectionContextTest {
    
    public function testIsUp() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testVersion() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('version' . PHP_EOL);
                    break;
                case 1:
                    $this->assertEquals("PhuninNode on HOSTNAME version: 0.2.2\n", $data);
                    $this->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testList() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('list' . PHP_EOL);
                    break;
                case 1:
                    $this->assertEquals("plugins plugins_categories memory_usage\n", $data);
                    $this->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testConfig() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('config memory_usage' . PHP_EOL);
                    break;
                case 1:
                    $this->assertEquals("graph_category phunin_node\ngraph_title Memory Usage\nmemory_usage.label Current Memory Usage\nmemory_peak_usage.label Peak Memory Usage\n.\n", $data);
                    $this->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testFetch() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('fetch plugins_categories' . PHP_EOL);
                    break;
                case 1:
                    $this->assertEquals("phunin_node.value 3\n.\n", $data);
                    $this->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testNodes() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('nodes' . PHP_EOL);
                    break;
                case 1:
                    $this->assertEquals("HOSTNAME\n", $data);
                    $this->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testUnknownCommand() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('unknown_command' . PHP_EOL);
                    break;
                case 1:
                    $this->assertEquals("# Unknown command. Try list, nodes, version, config, fetch or quit\n", $data);
                    $this->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testConfigUnknownPlugin() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('config unknown_plugin' . PHP_EOL);
                    break;
                case 1:
                    $this->assertTrue(false);
                    break;
            }
            $i++;
        });
        $this->loop->run();
        $this->assertTrue(true);
    }
    
    public function testFetchUnknownPlugin() {
        $i = 0;
        
        $this->conn->on('data', function ($data) use (&$i) {
            switch ($i) {
                case 0:
                    $this->assertEquals("# munin node at HOSTNAME\n", $data);
                    $this->conn->write('fetch unknown_plugin' . PHP_EOL);
                    break;
                case 1:
                    $this->assertTrue(false);
                    break;
            }
            $i++;
        });
        $this->loop->run();
        $this->assertTrue(true);
    }
    
}