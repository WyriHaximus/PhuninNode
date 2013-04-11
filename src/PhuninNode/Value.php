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

class Value {
    
    private $key;
    
    private $value;
    
    public function setKey($key) {
        $this->key = $key;
    }
    
    public function getKey() {
        return $this->key;
    }
    
    public function setValue($value) {
        $this->value = $value;
    }
    
    public function getValue() {
        return $this->value;
    }
}