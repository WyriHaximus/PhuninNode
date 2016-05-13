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
use function React\Promise\resolve;
use WyriHaximus\PhuninNode\ConnectionContext;

class Cap implements CommandInterface
{
    use NodeAwareTrait;

    /**
     * @param ConnectionContext $context
     * @param string $line
     * @return PromiseInterface
     */
    public function handle(ConnectionContext $context, string $line): PromiseInterface
    {
        $caps = [];
        foreach ($this->getNode()->getPlugins() as $plugin) {
            foreach ($plugin->getCapabilities() as $capability) {
                if (in_array($capability, $caps)) {
                    continue;
                }

                $caps[] = $capability;
            }
        }
        return resolve($caps);
    }
}
