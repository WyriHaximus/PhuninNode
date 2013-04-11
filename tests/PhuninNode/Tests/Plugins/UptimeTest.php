<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhuninNode\Tests\Plugins;

class UptimeTest extends AbstractPluginTest {
    
    public function setUp() {
        parent::setUp();
        $this->plugin = new \PhuninNode\Plugins\Uptime();
        $this->node->addPlugin($this->plugin);
    }
    
}