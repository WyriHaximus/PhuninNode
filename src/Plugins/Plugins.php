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
        return 'plugins';
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
        $this->configuration->setPair('graph_title', 'Plugins loaded');
        $this->configuration->setPair('plugins_count.label', 'Plugin Count');
        $this->configuration->setPair('plugins_category_count.label', 'Plugin Category Count');

        return \React\Promise\resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        $promises = [];
        $promises[] = $this->getPluginCountValue();
        $promises[] = $this->getPluginCategoryCountValue();
        return \WyriHaximus\PhuninNode\valuePromisesToObjectStorage($promises);
    }

    /**
     * @return Value
     */
    private function getPluginCountValue()
    {
        return \React\Promise\resolve(new Value('plugins_count', $this->node->getPlugins()->count()));
    }

    /**
     * @return Value
     */
    private function getPluginCategoryCountValue()
    {
        $categories = [];
        $plugins = $this->node->getPlugins();
        foreach ($plugins as $plugin) {
            $category = $plugin->getCategorySlug();
            $categories[$category] = true;
        }

        return \React\Promise\resolve(new Value('plugins_category_count', count($categories)));
    }
}
