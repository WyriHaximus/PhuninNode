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
use WyriHaximus\PhuninNode\Node;
use WyriHaximus\PhuninNode\PluginInterface;
use function React\Promise\resolve;

/**
 * Class MultiGraph
 * @package WyriHaximus\PhuninNode\Plugins
 */
class MultiGraph implements PluginInterface
{
    /**
     * @var Node
     */
    private $node;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $category;

    /**
     * @param string $slug
     * @param string $category
     */
    public function __construct(string $slug, string $category)
    {
        $this->slug = $slug;
        $this->category = $category;
    }

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
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategorySlug(): string
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): PromiseInterface
    {
        return resolve([]);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): PromiseInterface
    {
        return resolve([]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCapabilities(): array
    {
        return [
            'multigraph'
        ];
    }
}
