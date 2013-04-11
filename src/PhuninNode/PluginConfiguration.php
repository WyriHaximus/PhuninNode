<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhuninNode;

class PluginConfiguration {
    
    private $pairs = array();
    
    public function setPair($key, $value) {
        $pair = new Value();
        $pair->setKey($key);
        $pair->setValue($value);
        $this->pairs[$key] = $pair;
    }
    
    public function getPair($key) {
        return $this->pairs[$key];
    }
    
    public function getPairs() {
        return $this->pairs;
    }
    
}