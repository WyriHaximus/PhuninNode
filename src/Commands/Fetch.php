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

namespace WyriHaximus\PhuninNode\Commands;

use React\Promise\PromiseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;
use WyriHaximus\PhuninNode\ConnectionContext;

class Fetch implements CommandInterface
{
    use NodeAwareTrait;

    /**
     * @param ConnectionContext $context
     * @param string $line
     * @return PromiseInterface
     */
    public function handle(ConnectionContext $context, string $line): PromiseInterface
    {
        if ($line === '') {
            $context->quit();
            return reject();
        }

        $plugin = $this->getNode()->getPlugin($line);

        if ($plugin === false) {
            $context->quit();
            return reject();
        }

        return $plugin->getValues()->then(
            function ($values) {
                $lines = [];
                foreach ($values as $value) {
                    $lines[] = $value->getKey() . '.value ' . str_replace(',', '.', $value->getValue());
                }
                $lines[] = '.';
                return resolve($lines);
            },
            function () {
                return resolve([
                    '.',
                ]);
            }
        );
    }
}
