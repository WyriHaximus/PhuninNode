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

class MemoryUsage implements \WyriHaximus\PhuninNode\PluginInterface
{
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
     * @inheretDoc
     */
    public function setNode(\WyriHaximus\PhuninNode\Node $node)
    {
        $this->node = $node;
    }

    /**
     * @inheretDoc
     */
    public function getSlug()
    {
        return 'memory_usage';
    }

    /**
     * @inheretDoc
     */
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver)
    {
        if ($this->configuration instanceof \WyriHaximus\PhuninNode\PluginConfiguration) {
            $deferredResolver->resolve($this->configuration);
            return;
        }

        $this->configuration = new \WyriHaximus\PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Memory Usage');
        $this->configuration->setPair('memory_usage.label', 'Current Memory Usage');
        $this->configuration->setPair('memory_peak_usage.label', 'Peak Memory Usage');

        $deferredResolver->resolve($this->configuration);
    }

    /**
     * @inheretDoc
     */
    public function getValues(\React\Promise\DeferredResolver $deferredResolver)
    {
        $values = new \SplObjectStorage;
        $values->attach($this->getMemoryUsageValue());
        $values->attach($this->getMemoryPeakUsageValue());
        $deferredResolver->resolve($values);
    }

    private function getMemoryUsageValue()
    {

        $value = new \WyriHaximus\PhuninNode\Value();
        $value->setKey('memory_usage');
        $value->setValue(memory_get_usage(true));

        return $value;
    }

    private function getMemoryPeakUsageValue()
    {

        $value = new \WyriHaximus\PhuninNode\Value();
        $value->setKey('memory_peak_usage');
        $value->setValue(memory_get_peak_usage(true));

        return $value;
    }
}
