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
    public function getCategorySlug()
    {
        return 'phunin_node';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $configuration = new Configuration();
        $configuration->setPair('graph_category', 'phunin_node');
        $configuration->setPair('graph_title', 'Plugins Per Category');

        foreach ($this->getPluginCategories() as $key => $value) {
            $configuration->setPair($key . '.label', $key);
        }

        return \React\Promise\resolve($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return \WyriHaximus\PhuninNode\valuePromisesToObjectStorage(
            \WyriHaximus\PhuninNode\arrayToValuePromises(
                $this->getPluginCategories()
            )
        );
    }

    /**
     * @return array
     */
    private function getPluginCategories()
    {
        $categories = [];
        $plugins = $this->node->getPlugins();
        foreach ($plugins as $plugin) {
            $category = $plugin->getCategorySlug();
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
        }

        return $categories;
    }
}
