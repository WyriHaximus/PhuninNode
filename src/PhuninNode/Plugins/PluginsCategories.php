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

class PluginsCategories implements \PhuninNode\Interfaces\Plugin {
    private $node;
    private $categories = array();
    private $values = array();
    private $configuration;
    public function setNode(\PhuninNode\Node $node) {
        $this->node = $node;
    }
    
    public function getSlug() {
        return 'plugins_categories';
    }
    
    public function getConfiguration() {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            return $this->configuration;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Plugin Per Categories');
        
        foreach ($this->getPluginCategories() as $key => $value) {
            $this->configuration->setPair($key . '.label', $key);
        }
        
        return $this->configuration;
    }
    
    public function getValues() {
        $values = new \SplObjectStorage;
        foreach ($this->getPluginCategories() as $key => $value) {
            $values->attach($this->getPluginCategoryValue($key));
        }
        return $values;
    }
    
    private function getPluginCategories() {
        if (count($this->categories) > 0) {
            return $this->categories;
        }
        
        $plugins = $this->node->getPlugins();
        foreach ($plugins as $plugin) {
            $category = $plugin->getConfiguration()->getPair('graph_category')->getValue();
            if (!isset($this->categories[$category])) {
                $this->categories[$category] = 0;
            }
            
            $this->categories[$category]++;
        }
        
        return $this->categories;
    }
    
    private function getPluginCategoryValue($key) {
        if (isset($this->values[$key]) && $this->values[$key] instanceof \PhuninNode\Value) {
            return $this->values[$key];
        }
        
        $this->values[$key] = new \PhuninNode\Value();
        $this->values[$key]->setKey($key);
        $this->values[$key]->setValue($this->getPluginCategories()[$key]);
        
        return $this->values[$key];
    }
}