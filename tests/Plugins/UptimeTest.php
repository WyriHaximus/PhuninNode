<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests\Plugins;

use WyriHaximus\PhuninNode\Plugins\Uptime;

/**
 * Class UptimeTest
 * @package WyriHaximus\PhuninNode\Tests\Plugins
 */
class UptimeTest extends AbstractPluginTest
{

    public function setUp()
    {
        $this->plugin = new Uptime();

        parent::setUp();

        $this->node->addPlugin($this->plugin);
    }
}
