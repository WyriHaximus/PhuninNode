<?php
declare(strict_types=1);

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
     * @var float
     */
    protected $value;

    /**
     * @param string $key
     * @param float $value
     */
    public function __construct(string $key, float $value)
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
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
