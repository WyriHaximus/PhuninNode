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

/**
 * Class Uptime
 * @package WyriHaximus\PhuninNode\Plugins
 */
class Uptime implements PluginInterface
{
    /**
     * Seconds in a day
     */
    const DAY_IN_SECONDS = 86400;

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
     * Start time of this instance
     *
     * @var int
     */
    private $startTime = 0;

    /**
     * Save the time this instance started
     */
    public function __construct()
    {
        $this->startTime = time();
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
    public function getSlug(): string
    {
        return 'uptime';
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
            return \React\Promise\resolve($this->configuration);
        }

        $this->configuration = new Configuration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Uptime');
        $this->configuration->setPair('graph_args', '--base 1000 -l 0');
        $this->configuration->setPair('graph_vlabel', 'uptime in days');
        $this->configuration->setPair('uptime.label', 'uptime');
        $this->configuration->setPair('uptime.draw', 'AREA');

        return \React\Promise\resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): PromiseInterface
    {
        return \WyriHaximus\PhuninNode\metricPromisesToObjectStorage([
            $this->getUptimeValue(),
        ]);
    }

    /**
     * @return Metric
     */
    private function getUptimeValue(): Metric
    {
        $value = new Metric(
            'uptime',
            round(((time() - $this->startTime) / self::DAY_IN_SECONDS), 2)
        );
        return $value;
    }
}
