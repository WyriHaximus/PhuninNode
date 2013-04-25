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

class ConnectionTest extends AbstractConnectionTest {
    
    public function testIsUp() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testVersion() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('version' . PHP_EOL);
                    break;
                case 1:
                    $that->assertEquals("PhuninNode on HOSTNAME version: 0.2.0-DEV\n", $data);
                    $that->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testList() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('list' . PHP_EOL);
                    break;
                case 1:
                    $that->assertEquals("plugins plugins_categories memory_usage\n", $data);
                    $that->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testConfig() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('config memory_usage' . PHP_EOL);
                    break;
                case 1:
                    $that->assertEquals("graph_category phunin_node\ngraph_title Memory Usage\nmemory_usage.label Current Memory Usage\nmemory_peak_usage.label Peak Memory Usage\n.\n", $data);
                    $that->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testFetch() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('fetch plugins_categories' . PHP_EOL);
                    break;
                case 1:
                    $that->assertEquals("phunin_node.value 3\n.\n", $data);
                    $that->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testNodes() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('nodes' . PHP_EOL);
                    break;
                case 1:
                    $that->assertEquals("HOSTNAME\n", $data);
                    $that->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testUnknownCommand() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('unknown_command' . PHP_EOL);
                    break;
                case 1:
                    $that->assertEquals("# Unknown command. Try list, nodes, version, config, fetch or quit\n", $data);
                    $that->conn->write('quit' . PHP_EOL);
                    break;
            }
            $i++;
        });
        $this->loop->run();
    }
    
    public function testConfigUnknownPlugin() {
        $i = 0;
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('config unknown_plugin' . PHP_EOL);
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
        $that = $this;
        $this->conn->on('data', function ($data) use ($that, &$i) {
            switch ($i) {
                case 0:
                    $that->assertEquals("# munin node at HOSTNAME\n", $data);
                    $that->conn->write('fetch unknown_plugin' . PHP_EOL);
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