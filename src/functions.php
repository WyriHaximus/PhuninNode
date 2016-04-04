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

use React\Promise\PromiseInterface;

/**
 * @param array $promises
 * @return PromiseInterface
 */
function metricPromisesToObjectStorage(array $promises): PromiseInterface
{
    return \React\Promise\all($promises)->then(function ($values) {
        $storage = new \SplObjectStorage();
        foreach ($values as $value) {
            $storage->attach($value);
        }
        return \React\Promise\resolve($storage);
    });
}

/**
 * @param array $array
 * @return \Generator
 */
function arrayToValuePromises(array $array): \Generator
{
    foreach ($array as $key => $value) {
        yield \React\Promise\resolve(new Value($key, $value));
    }
}

/**
 * @param array $array
 * @return \Generator
 */
function arrayToMetricPromises(array $array): \Generator
{
    foreach ($array as $key => $value) {
        yield \React\Promise\resolve(new Metric($key, $value));
    }
}
