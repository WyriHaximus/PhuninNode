<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Tests;

class NodeTest extends \PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->loop = $this->getMock('\React\EventLoop\StreamSelectLoop');
		$this->socket = $this->getMock('\React\Socket\Server', [
			'on',
			'write',
			'shutdown',
		], [
			$this->loop,
			'0.0.0.0',
			12345
		]);

		$this->plugins = new \SplObjectStorage();
		$plugin = $this->getMock('\WyriHaximus\PhuninNode\Interfaces\Plugin', [
			'getSlug',
			'getConfiguration',
			'setNode',
			'getValues',
		]);
		$plugin->method('getSlug')
			->willReturn('a');
		$plugin->method('getConfiguration')
			->will($this->returnCallback(function($resolver) {
				$configuration = new \WyriHaximus\PhuninNode\PluginConfiguration();
				$configuration->setPair('graph_category', 'a');
				$resolver->resolve($configuration);
			}));
		$this->plugins->attach($plugin);
	}

	public function tearDown() {
		unset($this->loop, $this->socket, $this->plugins);
	}

	public function testConstruct() {
		$this->socket->expects($this->once())
			->method('on')
			->with('connection', $this->callback(function($callback) {
				return $this->isInstanceOf('\WyriHaximus\PhuninNode\Node', $callback[0]) && $this->identicalTo('onConnection', $callback[1]);
			}));
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
	}

	public function testShutdown() {
		$this->socket->expects($this->once())
			->method('shutdown');
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$node->shutdown();
	}

	public function testOnConnection() {
		$connection = $this->getMock('\React\Socket\Connection', [], [
			$this->socket,
			$this->loop,
		]);
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$node->onConnection($connection);
		$connections = $node->getConnections();
		$connections->rewind();
		$this->assertSameSize(['a'], $connections);
		$this->assertInstanceOf('\WyriHaximus\PhuninNode\ConnectionContext', $connections->current());
	}

	public function testOnClose() {
		$connection = $this->getMock('\React\Socket\Connection', [], [
			$this->socket,
			$this->loop,
		]);
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$node->onConnection($connection);
		$connections = $node->getConnections();
		$connections->rewind();
		$node->onClose($connections->current());
		$this->assertSameSize([], $node->getConnections());
	}

	public function testAddPlugin() {
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$this->assertSameSize([], $node->getPlugins());
		$this->plugins->rewind();
		$node->addPlugin($this->plugins->current());
		$this->assertSameSize(['a'], $node->getPlugins());
	}

	public function testGetPluginsEmpty() {
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$this->assertSameSize([], $node->getPlugins());
	}

	public function testGetPluginsOne() {
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$this->plugins->rewind();
		$node->addPlugin($this->plugins->current());
		$this->assertSameSize(['a'], $node->getPlugins());
	}

	public function testGetConnectionsEmpty() {
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$this->assertSameSize([], $node->getConnections());
	}

	public function testGetConnectionsOne() {
		$connection = $this->getMock('\React\Socket\Connection', [], [
			$this->socket,
			$this->loop,
		]);
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$node->onConnection($connection);
		$this->assertSameSize(['a'], $node->getConnections());
	}

	public function testGetPlugin() {
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$this->plugins->rewind();
		$node->addPlugin($this->plugins->current());
		$this->assertSame($this->plugins->current(), $node->getPlugin('a'));
		$this->assertTrue(!$node->getPlugin('b'));
	}

	public function testResolverFactory() {
		$called = false;
		$node = new \WyriHaximus\PhuninNode\Node($this->loop, $this->socket);
		$resolver = $node->resolverFactory(function() use (&$called) {
			$called = true;
		});
		$resolver->resolve();

		$this->assertTrue($called);
	}
    
}