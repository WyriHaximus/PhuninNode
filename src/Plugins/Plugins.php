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
use WyriHaximus\PhuninNode\Metric;
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;
use function React\Promise\resolve;
use function WyriHaximus\PhuninNode\metricPromisesToObjectStorage;

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
    public function getSlug(): string
    {
        return 'plugins';
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
            return resolve($this->configuration);
        }

        $this->configuration = new Configuration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Plugins loaded');
        $this->configuration->setPair('plugins_count.label', 'Plugins');
        $this->configuration->setPair('plugins_category_count.label', 'Plugin Categories');

        return resolve($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): PromiseInterface
    {
        return metricPromisesToObjectStorage([
            $this->getPluginCountValue(),
            $this->getPluginCategoryCountValue(),
        ]);
    }

    /**
     * @return Metric
     */
    private function getPluginCountValue(): Metric
    {
        return new Metric('plugins_count', $this->node->getPlugins()->count());
    }

    /**
     * @return Metric
     */
    private function getPluginCategoryCountValue(): Metric
    {
        $categories = [];
        $plugins = $this->node->getPlugins();
        foreach ($plugins as $plugin) {
            $category = $plugin->getCategorySlug();
            $categories[$category] = true;
        }

        return new Metric('plugins_category_count', count($categories));
    }
}
