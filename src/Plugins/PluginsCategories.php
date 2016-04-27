<?php
declare(strict_types=1);

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode\Plugins;

use React\Promise\PromiseInterface;
use WyriHaximus\PhuninNode\Configuration;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;
use function React\Promise\resolve;
use function WyriHaximus\PhuninNode\arrayToMetricPromises;
use function WyriHaximus\PhuninNode\metricPromisesToObjectStorage;

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
    public function getSlug(): string
    {
        return 'plugins_categories';
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
        $configuration = new Configuration();
        $configuration->setPair('graph_category', 'phunin_node');
        $configuration->setPair('graph_title', 'Plugins Per Category');

        foreach ($this->getPluginCategories() as $key => $value) {
            $configuration->setPair($key . '.label', $key);
        }

        return resolve($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): PromiseInterface
    {
        return metricPromisesToObjectStorage(
            iterator_to_array(
                arrayToMetricPromises(
                    $this->getPluginCategories()
                )
            )
        );
    }

    /**
     * @return array
     */
    private function getPluginCategories(): array
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
