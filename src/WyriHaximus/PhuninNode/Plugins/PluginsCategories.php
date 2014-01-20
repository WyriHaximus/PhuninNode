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
 * Class PluginsCategories
 * @package WyriHaximus\PhuninNode\Plugins
 */
class PluginsCategories implements \WyriHaximus\PhuninNode\PluginInterface
{
    /**
     * @var \WyriHaximus\PhuninNode\Node
     */
    private $node;

    /**
     * @var array
     */
    private $categories = [];

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
     * @inheretDoc
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
        return 'plugins_categories';
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
        $this->configuration->setPair('graph_title', 'Plugin Per Categories');

        foreach ($this->getPluginCategories() as $key => $value) {
            $this->configuration->setPair($key . '.label', $key);
        }

        $deferredResolver->resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(\React\Promise\DeferredResolver $deferredResolver)
    {
        $values = new \SplObjectStorage;
        foreach ($this->getPluginCategories() as $key => $value) {
            $values->attach($this->getPluginCategoryValue($key));
        }
        $deferredResolver->resolve($values);
    }

    /**
     * @return array
     */
    private function getPluginCategories()
    {
        if (count($this->categories) > 0) {
            return $this->categories;
        }

        $plugins = $this->node->getPlugins();
        foreach ($plugins as $plugin) {
            $deferred = new \React\Promise\Deferred();
            $deferred->promise()->then(
                function ($configuration) {
                    $category = $configuration->getPair('graph_category')->getValue();
                    if (!isset($this->categories[$category])) {
                        $this->categories[$category] = 0;
                    }
                    $this->categories[$category]++;
                }
            );
            $plugin->getConfiguration($deferred->resolver());
        }

        return $this->categories;
    }

    /**
     * @param $key
     * @return \WyriHaximus\PhuninNode\Value
     */
    private function getPluginCategoryValue($key)
    {
        if (isset($this->values[$key]) && $this->values[$key] instanceof \WyriHaximus\PhuninNode\Value) {
            return $this->values[$key];
        }

        $this->values[$key] = new \WyriHaximus\PhuninNode\Value();
        $this->values[$key]->setKey($key);
        $categories = $this->getPluginCategories();
        $this->values[$key]->setValue($categories[$key]);

        return $this->values[$key];
    }
}
