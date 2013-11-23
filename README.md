PhuninNode
==========

[![Build Status](https://secure.travis-ci.org/WyriHaximus/PhuninNode.png)](http://travis-ci.org/WyriHaximus/PhuninNode) [![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/WyriHaximus/phuninnode/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

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

// Bind to IP and port
$node = new \PhuninNode\Node($loop, 12345, '0.0.0.0');

// Add plugins
$node->addPlugin(new \PhuninNode\Plugins\Plugins());
$node->addPlugin(new \PhuninNode\Plugins\PluginsCategories());
$node->addPlugin(new \PhuninNode\Plugins\MemoryUsage());
$node->addPlugin(new \PhuninNode\Plugins\Uptime());

// Get rolling
$loop->run();
```

## Todo ##

- Async support
- Further work out munin protocol support
- Wildcard plugins like [this](http://munin-monitoring.org/browser/munin/plugins/node.d.linux/if_.in)

## License ##

Copyright 2013 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

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

