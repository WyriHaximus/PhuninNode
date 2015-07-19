<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Plugins;

use React\Promise\Deferred;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\Configuration;
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
    public function getConfiguration(Deferred $deferred)
    {
        if ($this->configuration instanceof Configuration) {
            $deferred->resolve($this->configuration);
            return;
        }

        $this->configuration = new Configuration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Memory Usage');
        $this->configuration->setPair('memory_usage.label', 'Current Memory Usage');
        $this->configuration->setPair('memory_peak_usage.label', 'Peak Memory Usage');

        $deferred->resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(Deferred $deferred)
    {
        $values = new \SplObjectStorage;
        $values->attach($this->getMemoryUsageValue());
        $values->attach($this->getMemoryPeakUsageValue());
        $deferred->resolve($values);
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
