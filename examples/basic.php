<?php

use React\EventLoop\Factory;
use WyriHaximus\PhuninNode\Factory as NodeFactory;
use WyriHaximus\PhuninNode\Plugins;

require dirname(__DIR__) . '/vendor/autoload.php';

// Create eventloop
$loop = Factory::create();

// Create Node
$node = NodeFactory::create($loop, '0.0.0.0', 12345);

// Add plugins
$node->addPlugin(new Plugins\Plugins());
$node->addPlugin(new Plugins\PluginsCategories());
$node->addPlugin(new Plugins\MemoryUsage());
$node->addPlugin(new Plugins\Uptime());

// Get rolling
$loop->run();
