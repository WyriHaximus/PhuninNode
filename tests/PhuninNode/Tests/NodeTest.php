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

class NodeTest extends AbstractConnectionContextTest {
    
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
    
}