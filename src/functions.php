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
 * @param $promises
 * @return \React\Promise\PromiseInterface
 */
function valuePromisesToObjectStorage($promises)
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
 * @param $array
 * @return \Generator
 */
function arrayToValuePromises($array)
{
    foreach ($array as $key => $value) {
        yield \React\Promise\resolve(new Value($key, $value));
    }
}
