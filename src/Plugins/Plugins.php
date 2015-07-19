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
use WyriHaximus\PhuninNode\PluginConfiguration;
use WyriHaximus\PhuninNode\PluginInterface;
use WyriHaximus\PhuninNode\Value;

/**
 * Class Plugins
 * @package WyriHaximus\PhuninNode\Plugins
 */
class Plugins implements PluginInterface
{
    /**
     * @var Node
     */
    private $node;

    /**
     * @var array
     */
    private $values = [];

    /**
     * Cached configuration state
     *
     * @var PluginConfiguration
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
        return 'plugins';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(Deferred $deferred)
    {
        if ($this->configuration instanceof PluginConfiguration) {
            $deferred->resolve($this->configuration);
            return;
        }

        $this->configuration = new PluginConfiguration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Plugins loaded');
        $this->configuration->setPair('plugins_count.label', 'Plugin Count');
        $this->configuration->setPair('plugins_category_count.label', 'Plugin Category Count');

        $deferred->resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(Deferred $deferred)
    {
        $values = new \SplObjectStorage;
        $values->attach($this->getPluginCountValue());
        $values->attach($this->getPluginCategoryCountValue());
        $deferred->resolve($values);
    }

    /**
     * @return Value
     */
    private function getPluginCountValue()
    {
        if (isset($this->values['plugins_count']) &&
            $this->values['plugins_count'] instanceof Value) {
            return $this->values['plugins_count'];
        }

        $this->values['plugins_count'] = new Value('plugins_count', $this->node->getPlugins()->count());

        return $this->values['plugins_count'];
    }

    /**
     * @return Value
     */
    private function getPluginCategoryCountValue()
    {
        if (isset($this->values['plugins_category_count']) &&
            $this->values['plugins_category_count'] instanceof Value) {
            return $this->values['plugins_category_count'];
        }

        $categories = [];
        $plugins = $this->node->getPlugins();
        foreach ($plugins as $plugin) {
            $deferred = new Deferred();
            $deferred->promise()->then(
                function ($configuration) use (&$categories) {
                    $category = $configuration->getPair('graph_category')->getValue();
                    if (!isset($categories[$category])) {
                        $categories[$category] = true;
                    }
                }
            );
            $plugin->getConfiguration($deferred);
        }

        $this->values['plugins_category_count'] = new Value('plugins_category_count', count($categories));

        return $this->values['plugins_category_count'];
    }
}
