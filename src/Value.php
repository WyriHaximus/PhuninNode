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
 * Class Value
 * @package WyriHaximus\PhuninNode
 */
class Value
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key = null, string $value = null)
    {
        $this->setKey($key);
        $this->setValue($value);
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param mixed $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
