PhuninNode
==========

PhuninNode is a munin-node port PHP aiming to provide application monitoring utilizing munin.

## Installation ##

Installation is easy with composer just add PhuninNode to your composer.json.

    {
        "require": {
            "wyrihaximus/phunin-node": "dev-master"
        }
    }

## Basic usage ##

    // Create eventloop
    $loop = \React\EventLoop\Factory::create();
    
    // Bind to IP and port
    $node = new \PhuninNode\Node($loop, 12345, '0.0.0.0');
    
    // Add plugins
    $node->addPlugin(new \PhuninNode\Plugins\Plugins());
    $node->addPlugin(new \PhuninNode\Plugins\PluginsCategories());
    $node->addPlugin(new \PhuninNode\Plugins\MemoryUsage());
    $node->addPlugin(new \PhuninNode\Plugins\Uptime());
    
    // Get rolling
    $loop->run();