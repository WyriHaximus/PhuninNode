<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Plugins;

/**
 * Class Uptime
 * @package WyriHaximus\PhuninNode\Plugins
 */
class Uptime implements \WyriHaximus\PhuninNode\PluginInterface
{
    /**
     * Seconds in a day
     */
    const DAY_IN_SECONDS = 86400;

    /**
     * @var \WyriHaximus\PhuninNode\Node
     */
    private $node;

    /**
     * Cached configuration state
     *
     * @var \WyriHaximus\PhuninNode\PluginConfiguration
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
    public function setNode(\WyriHaximus\PhuninNode\Node $node)
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
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver)
    {
        if ($this->configuration instanceof \WyriHaximus\PhuninNode\PluginConfiguration) {
            $deferredResolver->resolve($this->configuration);
            return;
        }

        $this->configuration = new \WyriHaximus\PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Uptime');
        $this->configuration->setPair('graph_args', '--base 1000 -l 0');
        $this->configuration->setPair('graph_vlabel', 'uptime in days');
        $this->configuration->setPair('uptime.label', 'uptime');
        $this->configuration->setPair('uptime.draw', 'AREA');

        $deferredResolver->resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(\React\Promise\DeferredResolver $deferredResolver)
    {
        $values = new \SplObjectStorage;
        $values->attach($this->getUptimeValue());
        $deferredResolver->resolve($values);
    }

    private function getUptimeValue()
    {
        $value = new \WyriHaximus\PhuninNode\Value();
        $value->setKey('uptime');
        $value->setValue(round(((time() - $this->startTime) / self::DAY_IN_SECONDS), 2));
        return $value;
    }
}
