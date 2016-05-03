<?php

declare(strict_types=1);
/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Plugins;

use React\Promise\Deferred;
use WyriHaximus\PhuninNode\Configuration;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;
use WyriHaximus\PhuninNode\Value;

/**
 * Class MultiGraph
 * @package WyriHaximus\PhuninNode\Plugins
 */
class MultiGraph implements PluginInterface
{
    /**
     * @var Node
     */
    private $node;

    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * @param PluginInterface[] $plugins
     */
    public function __construct(array $plugins)
    {
        $this->plugns = $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function setNode(Node $node)
    {
        $this->node = $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return 'uptime';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(Deferred $deferred)
    {
        $this->configuration = new Configuration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Uptime');
        $this->configuration->setPair('graph_args', '--base 1000 -l 0');
        $this->configuration->setPair('graph_vlabel', 'uptime in days');
        $this->configuration->setPair('uptime.label', 'uptime');
        $this->configuration->setPair('uptime.draw', 'AREA');

        $deferred->resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(Deferred $deferred)
    {
        $values = new \SplObjectStorage;
        $values->attach($this->getUptimeValue());
        $deferred->resolve($values);
    }

    /**
     * @return \WyriHaximus\PhuninNode\Value
     */
    private function getUptimeValue()
    {
        $value = new Value();
        $value->setKey('uptime');
        $value->setValue(round(((time() - $this->startTime) / self::DAY_IN_SECONDS), 2));
        return $value;
    }
}
