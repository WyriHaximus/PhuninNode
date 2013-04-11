<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhuninNode\Interfaces;

interface Plugin {
    public function setNode(\PhuninNode\Node $node);
    public function getSlug();
    public function getConfiguration();
    public function getValues();
}