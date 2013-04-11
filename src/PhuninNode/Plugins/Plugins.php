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

class Plugins implements \PhuninNode\Interfaces\Plugin {
    private $node;
    private $values = array();
    private $configuration;
    public function setNode(\PhuninNode\Node $node) {
        $this->node = $node;
    }
    
    public function getSlug() {
        return 'plugins';
    }
    
    public function getConfiguration() {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            return $this->configuration;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Plugins loaded');
        $this->configuration->setPair('plugins_count.label', 'Plugin Count');
        $this->configuration->setPair('plugins_category_count.label', 'Plugin Category Count');
        
        return $this->configuration;
    }
    
    public function getValues() {
        $values = new \SplObjectStorage;
        $values->attach($this->getPluginCountValue());
        $values->attach($this->getPluginCategoryCountValue());
        return $values;
    }
    
    private function getPluginCountValue() {
        if (isset($this->values['plugins_count']) && $this->values['plugins_count'] instanceof \PhuninNode\Value) {
            return $this->values['plugins_count'];
        }
        
        $this->values['plugins_count'] = new \PhuninNode\Value();
        $this->values['plugins_count']->setKey('plugins_count');
        $this->values['plugins_count']->setValue($this->node->getPlugins()->count());
        
        return $this->values['plugins_count'];
    }
    
    private function getPluginCategoryCountValue() {
        if (isset($this->values['plugins_category_count']) && $this->values['plugins_category_count'] instanceof \PhuninNode\Value) {
            return $this->values['plugins_category_count'];
        }
        
        $categories = array();
        $count = 0;
        $plugins = $this->node->getPlugins();
        foreach ($plugins as $plugin) {
            $category = $plugin->getConfiguration()->getPair('graph_category')->getValue();
            if (!isset($categories[$category])) {
                $categories[$category] = true;
                $count++;
            }
        }
        unset($categories);
        
        $this->values['plugins_category_count'] = new \PhuninNode\Value();
        $this->values['plugins_category_count']->setKey('plugins_category_count');
        $this->values['plugins_category_count']->setValue($count);
        
        return $this->values['plugins_category_count'];
    }
}