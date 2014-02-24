Deployment
==========

Deploying PhuninNode is easy. All you need to have is a file to run. We'll be using a very basic setup as example:

```php
<?php

require 'vendor/autoload.php';

// Create eventloop
$loop = \React\EventLoop\Factory::create();

// Create a socket
$socket = new \React\Socket\Server($loop);
$socket->listen(12345, '0.0.0.0');

// Bind to IP and port
$node = new \WyriHaximus\PhuninNode\Node($loop, $socket);

// Add plugins
$node->addPlugin(new \WyriHaximus\PhuninNode\Plugins\Plugins());
$node->addPlugin(new \WyriHaximus\PhuninNode\Plugins\PluginsCategories());
$node->addPlugin(new \WyriHaximus\PhuninNode\Plugins\MemoryUsage());
$node->addPlugin(new \WyriHaximus\PhuninNode\Plugins\Uptime());

// Get rolling
$loop->run();
```

[Supervisor]