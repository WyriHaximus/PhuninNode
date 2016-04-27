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
use function React\Promise\all;
use function React\Promise\resolve;

/**
 * @param array $promises
 * @return PromiseInterface
 */
function metricPromisesToObjectStorage(array $promises): PromiseInterface
{
    return all($promises)->then(function ($values) {
        $storage = new \SplObjectStorage();
        foreach ($values as $value) {
            $storage->attach($value);
        }
        return resolve($storage);
    });
}

/**
 * @param array $array
 * @return \Generator
 */
function arrayToValuePromises(array $array): \Generator
{
    foreach ($array as $key => $value) {
        yield resolve(new Value($key, $value));
    }
}

/**
 * @param array $array
 * @return \Generator
 */
function arrayToMetricPromises(array $array): \Generator
{
    foreach ($array as $key => $value) {
        yield resolve(new Metric($key, $value));
    }
}
