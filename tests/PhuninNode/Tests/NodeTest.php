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

class NodeTest extends AbstractConnectionTest {
    
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
    
}