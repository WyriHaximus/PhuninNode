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
 * Class Plugins
 * @package WyriHaximus\PhuninNode\Plugins
 */
class Plugins implements \WyriHaximus\PhuninNode\PluginInterface
{
    /**
     * @var \WyriHaximus\PhuninNode\Node
     */
    private $node;

    /**
     * @var array
     */
    private $values = [];

    /**
     * Cached configuration state
     *
     * @var \WyriHaximus\PhuninNode\PluginConfiguration
     */
    private $configuration;

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
        return 'plugins';
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
        $this->configuration->setPair('graph_title', 'Plugins loaded');
        $this->configuration->setPair('plugins_count.label', 'Plugin Count');
        $this->configuration->setPair('plugins_category_count.label', 'Plugin Category Count');

        $deferredResolver->resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(\React\Promise\DeferredResolver $deferredResolver)
    {
        $values = new \SplObjectStorage;
        $values->attach($this->getPluginCountValue());
        $values->attach($this->getPluginCategoryCountValue());
        $deferredResolver->resolve($values);
    }

    /**
     * @return \WyriHaximus\PhuninNode\Value
     */
    private function getPluginCountValue()
    {
        if (isset($this->values['plugins_count']) &&
            $this->values['plugins_count'] instanceof \WyriHaximus\PhuninNode\Value) {
            return $this->values['plugins_count'];
        }

        $this->values['plugins_count'] = new \WyriHaximus\PhuninNode\Value();
        $this->values['plugins_count']->setKey('plugins_count');
        $this->values['plugins_count']->setValue($this->node->getPlugins()->count());

        return $this->values['plugins_count'];
    }

    /**
     * @return \WyriHaximus\PhuninNode\Value
     */
    private function getPluginCategoryCountValue()
    {
        if (isset($this->values['plugins_category_count']) &&
            $this->values['plugins_category_count'] instanceof \WyriHaximus\PhuninNode\Value) {
            return $this->values['plugins_category_count'];
        }

        $categories = [];
        $plugins = $this->node->getPlugins();
        foreach ($plugins as $plugin) {
            $deferred = new \React\Promise\Deferred();
            $deferred->promise()->then(
                function ($configuration) use (&$categories) {
                    $category = $configuration->getPair('graph_category')->getValue();
                    if (!isset($categories[$category])) {
                        $categories[$category] = true;
                    }
                }
            );
            $plugin->getConfiguration($deferred->resolver());
        }

        $this->values['plugins_category_count'] = new \WyriHaximus\PhuninNode\Value();
        $this->values['plugins_category_count']->setKey('plugins_category_count');
        $this->values['plugins_category_count']->setValue(count($categories));

        return $this->values['plugins_category_count'];
    }
}
