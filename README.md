PhuninNode
==========

[![Build Status](https://travis-ci.org/WyriHaximus/PhuninNode.png)](https://travis-ci.org/WyriHaximus/PhuninNode)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/Phunin-Node/v/stable.png)](https://packagist.org/packages/WyriHaximus/Phunin-Node)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/Phunin-Node/downloads.png)](https://packagist.org/packages/WyriHaximus/Phunin-Node)
[![Coverage Status](https://coveralls.io/repos/WyriHaximus/PhuninNode/badge.png)](https://coveralls.io/r/WyriHaximus/PhuninNode)
[![License](https://poser.pugx.org/wyrihaximus/phunin-node/license.png)](https://packagist.org/packages/wyrihaximus/phunin-node)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/WyriHaximus/phuninnode/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7aa7729b-fc93-4e81-81be-56a6e9fcaa26/mini.png)](https://insight.sensiolabs.com/projects/7aa7729b-fc93-4e81-81be-56a6e9fcaa26)

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

## Simple usage ##

PhuninNode now also comes with a commandline tool to easily start instances based on a configuration file. It will look for the `phunin-node.yml` file containing the configuration. Just run `./vendor/bin/phunin-node` to start a new instance.

## Basic usage ##

```php
<?php

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

## Ideas ##

- Async support
- Further work out munin protocol support

## License ##

Copyright 2013 - 2014 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

