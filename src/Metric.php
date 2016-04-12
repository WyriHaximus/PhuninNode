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
    public function __construct(string $key, int $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
