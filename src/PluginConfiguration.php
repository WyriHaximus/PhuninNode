<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 - 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\PhuninNode;

/**
 * Class PluginConfiguration
 * @package WyriHaximus\PhuninNode
 */
class PluginConfiguration
{
    /**
     * @var array
     */
    private $pairs = [];

    /**
     * @param string $key
     * @param $value
     */
    public function setPair($key, $value)
    {
        $this->pairs[$key] = new Value($key, $value);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getPair($key)
    {
        return $this->pairs[$key];
    }

    /**
     * @return array
     */
    public function getPairs()
    {
        return $this->pairs;
    }
}
