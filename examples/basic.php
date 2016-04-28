<?php

use React\EventLoop\Factory;
use React\Socket\Server;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\Plugins\MemoryUsage;
use WyriHaximus\PhuninNode\Plugins\Plugins;
use WyriHaximus\PhuninNode\Plugins\PluginsCategories;
use WyriHaximus\PhuninNode\Plugins\Uptime;

require dirname(__DIR__) . '/vendor/autoload.php';

// Create eventloop
$loop = Factory::create();

// Create a socket
$socket = new Server($loop);
$socket->listen(12345, '0.0.0.0');

// Bind to IP and port
$node = new Node($loop, $socket);

// Add plugins
$node->addPlugin(new Plugins());
$node->addPlugin(new PluginsCategories());
$node->addPlugin(new MemoryUsage());
$node->addPlugin(new Uptime());

// Get rolling
$loop->run();