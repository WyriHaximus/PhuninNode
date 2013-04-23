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

class Uptime implements \PhuninNode\Interfaces\Plugin {
    
    const DAY_IN_SECONDS = 86400;
    
    private $node;
    private $configuration;
    private $startTime = 0;
    
    public function __construct() {
        $this->startTime = time();
    }
    
    public function setNode(\PhuninNode\Node $node) {
        $this->node = $node;
    }
    
    public function getSlug() {
        return 'uptime';
    }
    
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver) {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            $deferredResolver->resolve($this->configuration);
            return;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'phunin_node');
        $this->configuration->setPair('graph_title', 'Uptime');
        $this->configuration->setPair('graph_args', '--base 1000 -l 0');
        $this->configuration->setPair('graph_vlabel', 'uptime in days');
        $this->configuration->setPair('uptime.label', 'uptime');
        $this->configuration->setPair('uptime.draw', 'AREA');
        
        $deferredResolver->resolve($this->configuration);
    }
    
    public function getValues(\React\Promise\DeferredResolver $deferredResolver) {
        $values = new \SplObjectStorage;
        $values->attach($this->getUptimeValue());
        $deferredResolver->resolve($values);
    }
    
    private function getUptimeValue() {
        $value = new \PhuninNode\Value();
        $value->setKey('uptime');
        $value->setValue(round(((time() - $this->startTime) / self::DAY_IN_SECONDS), 2));
        return $value;
    }
}