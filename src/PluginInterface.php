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

namespace WyriHaximus\PhuninNode;

use React\Promise\PromiseInterface;

/**
 * Interface PluginInterface
 * @package WyriHaximus\PhuninNode
 */
interface PluginInterface
{
    /**
     * Sets the Node instance
     *
     * @param Node $node
     * @return void
     */
    public function setNode(Node $node);

    /**
     * Return the slug identifier for this plugin
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Return the category slug identifier for this plugin
     *
     * @return string
     */
    public function getCategorySlug(): string;

    /**
     * Get the configuration for this plugin, it should return a promise
     *
     * @return PromiseInterface
     */
    public function getConfiguration(): PromiseInterface;

    /**
     * Get the values for this plugin, it should return a promise
     *
     * @return PromiseInterface
     */
    public function getValues(): PromiseInterface;
}
