<?php

use React\EventLoop\Factory;
use WyriHaximus\PhuninNode\Factory as NodeFactory;
use WyriHaximus\PhuninNode\Plugins\MemoryUsage;
use WyriHaximus\PhuninNode\Plugins\Plugins;
use WyriHaximus\PhuninNode\Plugins\PluginsCategories;
use WyriHaximus\PhuninNode\Plugins\Uptime;

require dirname(__DIR__) . '/vendor/autoload.php';

// Create eventloop
$loop = Factory::create();

// Create Node
$node = NodeFactory::create($loop, '0.0.0.0', 12345);

// Add plugins
$node->addPlugin(new Plugins());
$node->addPlugin(new PluginsCategories());
$node->addPlugin(new MemoryUsage());
$node->addPlugin(new Uptime());

// Get rolling
$loop->run();
