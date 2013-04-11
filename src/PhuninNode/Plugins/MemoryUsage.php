<?php

/*
 * This file is part of PhuninNode.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhuninNode\Plugins;

class MemoryUsage implements \PhuninNode\Interfaces\Plugin {
    private $node;
    private $values = array();
    private $configuration;
    public function setNode(\PhuninNode\Node $node) {
        $this->node = $node;
    }
    
    public function getSlug() {
        return 'memory_usage';
    }
    
    public function getConfiguration() {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            return $this->configuration;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Memory Usage');
        $this->configuration->setPair('memory_usage.label', 'Current Memory Usage');
        $this->configuration->setPair('memory_peak_usage.label', 'Peak Memory Usage');
        
        return $this->configuration;
    }
    
    public function getValues() {
        $values = new \SplObjectStorage;
        $values->attach($this->getMemoryUsageValue());
        $values->attach($this->getMemoryPeakUsageValue());
        return $values;
    }
    
    private function getMemoryUsageValue() {
        
        $value = new \PhuninNode\Value();
        $value->setKey('memory_usage');
        $value->setValue(memory_get_usage(true));
        
        return $value;
    }
    
    private function getMemoryPeakUsageValue() {
        
        $value = new \PhuninNode\Value();
        $value->setKey('memory_peak_usage');
        $value->setValue(memory_get_peak_usage(true));
        
        return $value;
    }
}