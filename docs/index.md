PhuninNode
==========

PhuninNode is a munin-node port PHP aiming to provide application monitoring utilizing munin.

## Installation ##

Installation is easy with composer just add PhuninNode to your composer.json.

```json
{
	"require": {
		"wyrihaximus/phunin-node": "dev-master"
	}
}
```

## Basic usage ##

```php
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