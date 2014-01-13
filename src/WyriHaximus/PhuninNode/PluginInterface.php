<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode;

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
    public function setNode(\WyriHaximus\PhuninNode\Node $node);

    /**
     * Return the slug identifier for this plugin
     *
     * @return string
     */
    public function getSlug();

    /**
     * Get the configuration for this plugin, it should resolve the passed resolver
     *
     * @param \React\Promise\DeferredResolver $deferredResolver
     * @return void
     */
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver);

    /**
     * Get the values for this plugin, it should resolve the passed resolver
     *
     * @param \React\Promise\DeferredResolver $deferredResolver
     * @return void
     */
    public function getValues(\React\Promise\DeferredResolver $deferredResolver);
}
