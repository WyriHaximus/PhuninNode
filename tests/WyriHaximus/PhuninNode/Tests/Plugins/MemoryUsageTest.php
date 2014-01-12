<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests\Plugins;

class MemoryUsageTest extends AbstractPluginTest
{

    public function setUp()
    {
        $this->plugin = new \WyriHaximus\PhuninNode\Plugins\MemoryUsage();

        parent::setUp();

        $this->node->addPlugin($this->plugin);
    }

}