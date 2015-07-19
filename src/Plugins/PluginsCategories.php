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
 * Class PluginsCategories
 * @package WyriHaximus\PhuninNode\Plugins
 */
class PluginsCategories implements PluginInterface
{
    /**
     * @var Node
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
        return 'plugins_categories';
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
        $this->configuration->setPair('graph_title', 'Plugin Per Categories');

        foreach ($this->getPluginCategories() as $key => $value) {
            $this->configuration->setPair($key . '.label', $key);
        }

        $deferred->resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(Deferred $deferred)
    {
        $values = new \SplObjectStorage;
        foreach ($this->getPluginCategories() as $key => $value) {
            $values->attach($this->getPluginCategoryValue($key));
        }
        $deferred->resolve($values);
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
            $deferred = new Deferred();
            $deferred->promise()->then(
                function ($configuration) {
                    $category = $configuration->getPair('graph_category')->getValue();
                    if (!isset($this->categories[$category])) {
                        $this->categories[$category] = 0;
                    }
                    $this->categories[$category]++;
                }
            );
            $plugin->getConfiguration($deferred);
        }

        return $this->categories;
    }

    /**
     * @param $key
     * @return Value
     */
    private function getPluginCategoryValue($key)
    {
        if (isset($this->values[$key]) && $this->values[$key] instanceof Value) {
            return $this->values[$key];
        }

        $this->values[$key] = new Value($key, $this->getPluginCategories()[$key]);

        return $this->values[$key];
    }
}
