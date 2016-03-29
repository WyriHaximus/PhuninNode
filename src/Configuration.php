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
 * Class Configuration
 * @package WyriHaximus\PhuninNode
 */
class Configuration
{
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->setPair($key, $value);
        }
    }

    public function applyDefaults(array $defaults)
    {
        foreach ($defaults as $key => $value) {
            if (!$this->hasPair($key)) {
                $this->setPair($key, $value);
            }
        }
    }

    /**
     * @var array
     */
    private $pairs = [];

    /**
     * @param string $key
     * @param $value
     */
    public function setPair(string $key, $value)
    {
        $this->pairs[$key] = new Value($key, $value);
    }

    /**
     * @param string $key
     * @return Value
     */
    public function getPair(string $key): Value
    {
        return $this->pairs[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasPair($key): bool
    {
        return isset($this->pairs[$key]);
    }

    /**
     * @return array
     */
    public function getPairs(): array
    {
        return $this->pairs;
    }
}
