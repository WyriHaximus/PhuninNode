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
 * Class Metric
 * @package WyriHaximus\PhuninNode
 */
class Metric
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var int
     */
    protected $value;

    /**
     * @param string $key
     * @param int $value
     */
    public function __construct(string $key = null, int $value = null)
    {
        $this->setKey($key);
        $this->setValue($value);
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $value
     */
    public function setValue(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
