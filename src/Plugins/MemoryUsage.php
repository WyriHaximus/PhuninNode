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

use React\Promise\PromiseInterface;
use WyriHaximus\PhuninNode\Configuration;
use WyriHaximus\PhuninNode\Metric;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;
use function React\Promise\resolve;
use function WyriHaximus\PhuninNode\metricPromisesToObjectStorage;

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
    public function getSlug(): string
    {
        return 'memory_usage';
    }

    /**
     * {@inheritdoc}
     */
    public function getCategorySlug(): string
    {
        return 'phunin_node';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): PromiseInterface
    {
        if ($this->configuration instanceof Configuration) {
            return resolve($this->configuration);
        }

        $this->configuration = new Configuration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Memory Usage');
        $this->configuration->setPair('memory_usage.label', 'Current Memory Usage');
        $this->configuration->setPair('memory_peak_usage.label', 'Peak Memory Usage');
        $this->configuration->setPair('internal_memory_usage.label', 'Internal Current Memory Usage');
        $this->configuration->setPair('internal_memory_peak_usage.label', 'Internal Peak Memory Usage');

        return resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): PromiseInterface
    {
        return metricPromisesToObjectStorage([
            new Metric('memory_usage', memory_get_usage(true)),
            new Metric('memory_peak_usage', memory_get_peak_usage(true)),
            new Metric('internal_memory_usage', memory_get_usage()),
            new Metric('internal_memory_peak_usage', memory_get_peak_usage()),
        ]);
    }
}
