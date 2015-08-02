<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Plugins;

use WyriHaximus\PhuninNode\Configuration;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;
use WyriHaximus\PhuninNode\Value;

/**
 * Class MemoryUsage
 * @package WyriHaximus\PhuninNode\Plugins
 */
class MemoryUsage implements PluginInterface
{
    /**
     * @var Node
     */
    private $node;

    /**
     * Cached configuration state
     *
     * @var Configuration
     */
    private $configuration;

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
        return 'memory_usage';
    }

    /**
     * {@inheritdoc}
     */
    public function getCategorySlug()
    {
        return 'phunin_node';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        if ($this->configuration instanceof Configuration) {
            return \React\Promise\resolve($this->configuration);
        }

        $this->configuration = new Configuration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Memory Usage');
        $this->configuration->setPair('memory_usage.label', 'Current Memory Usage');
        $this->configuration->setPair('memory_peak_usage.label', 'Peak Memory Usage');

        return \React\Promise\resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        $promises = [];
        $promises[] = $this->getMemoryUsageValue();
        $promises[] = $this->getMemoryPeakUsageValue();
        return \WyriHaximus\PhuninNode\valuePromisesToObjectStorage($promises);
    }

    /**
     * @return Value
     */
    private function getMemoryUsageValue()
    {
        return new Value('memory_usage', memory_get_usage(true));
    }

    /**
     * @return Value
     */
    private function getMemoryPeakUsageValue()
    {
        return new Value('memory_peak_usage', memory_get_peak_usage(true));
    }
}
