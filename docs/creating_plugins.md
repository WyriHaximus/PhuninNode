Creating Plugins
================

Creating plugins for PhuninNode isn't hard but requires some thought and work. Over the course of the document we'll be creating a plugin to poll a `Cisco EPC3925` for `Upstream Power Level` values. First of all plugins must obey the following contract.

```php
interface Plugin {
    public function setNode(\PhuninNode\Node $node);
    public function getSlug();
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver);
    public function getValues(\React\Promise\DeferredResolver $deferredResolver);
}
```

Lets start with a basic plugin that does a couple of things. First it accepts the `\PhuninNode\Node` so we can query it for information (we don't really need that in this plugin but we are bound by contract to do so). Secondly we set a slug for this plugin serving as a uniquely identifying string. Thirdly is the `getConfiguration` function as you can see it needs a `\React\Promise\DeferredResolver` to work. This way it can do something asynchronously and once done return the configuration. For now it's synchronously, we'll change that later:

```php
class Cisco_EPC3925_Upstream_Power_Level implements \PhuninNode\Interfaces\Plugin {
    private $node;
    private $loop;
    private $configuration;
    
    public function setNode(\PhuninNode\Node $node) {
        $this->node = $node;
    }
    
    public function getSlug() {
        return 'cisco_epc3925_upstream_power_level';
    }
    
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver) {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            $deferredResolver->resolve($this->configuration);
            return;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'cisco_epc3925');
        $this->configuration->setPair('graph_title', 'Upstream Power Level');
        
        $deferredResolver->resolve($this->configuration);
    }
}
```

Lets start by added a `constant` to the class with the modems URL, or more specific the URL with the information we want.

```php
const CISCO_EPC3925_STATUS_URL = 'http://192.168.100.1/Docsis_system.asp';
```

Other constants we need are the DNS server to use.

```php
const DNS_SERVER_IP = '8.8.8.8';
```

And what `table` and `column` contains the data we require.

```php
const STATUS_TABLE = 3;
const STATUS_COLUMN = 2;
```

Before we'll add the correct logic to `getConfiguration` and `getValues` we need 2 helper methods fetching and parsing the information from the modem.

```php
    private function fetchModemStatusValue(\React\Promise\DeferredResolver $deferredResolver, $table, $column) {
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(function($html) use ($deferredResolver, $table, $column) {
            $channelValues = array();
            
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            
            $i = 0;
            $rows = $xpath->query('.//tr[position()>1]', $xpath->query('//table[contains(@class, \'std\')]')->item($table));
            foreach ($rows as $row) {
                $channelValues['channel' . ++$i] = (float) $xpath->query('.//td[' . $column . ']', $row)->item(0)->textContent;
            }
            
            $deferredResolver->resolve($channelValues);
        });
        $this->fetchModemStatusUrl($deferred->resolver());
    }
    
    private function fetchModemStatusUrl(\React\Promise\DeferredResolver $deferredResolver) {
        $client = $this->factory->create($this->loop, $this->dnsResolver);
        
        $request = $client->request('GET', self::CISCO_EPC3925_STATUS_URL);
        $request->on('response', function ($response) use ($deferredResolver) {
            $dataBuffer = new stdClass();
            $response->on('data', function ($data) use ($dataBuffer) {
                $dataBuffer->buffer .= $data;
            });
            $response->on('end', function () use ($dataBuffer , $deferredResolver) {
                $deferredResolver->resolve($dataBuffer->buffer);
            });
        });
        $request->end();
    }
```

And a `constructor` is required setting the loop and instancing the DNS resolved and HTTP Client factory.

```php
    public function __construct($loop) {
        $this->loop = $loop;
        
        $dnsResolverFactory = new \React\Dns\Resolver\Factory();
        $this->dnsResolver = $dnsResolverFactory->createCached(self::DNS_SERVER_IP, $this->loop);
        $this->factory = new \React\HttpClient\Factory();
    }
```

## getConfiguration ##

With these required methods in place we'll start on `getConfiguration`. First `$deferredResolver->resolve($configuration);` is removed from the method and replaced with:

```php
        $configuration = $this->configuration;
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(function($channels) use ($deferredResolver, $configuration) {
            
            $deferredResolver->resolve($configuration);
        });
```

That sets the promise up we need for the data retrieval. (We have to wait until the entire page is loaded and parsed but we also have to stay in non-blocking mode as another client might connect to the node.)

Within this closure we only need to add a foreach loop adding the configuration pairs for each channel.

```php
            foreach ($channels as $channel => $value) {
                $this->configuration->setPair($channel . '.min', 6);
                $this->configuration->setPair($channel . '.max', 12);
                $this->configuration->setPair($channel . '.label', $channel);
                $this->configuration->setPair($channel . '.type', 'GAUGE');
            }
```

Because the `getConfiguration` method caches the result of the first call we only have to request the configuration data once.

## getValues ##

`getValues` is just as simple as `getConfiguration` and starts with the same skelleton.

```php
    public function getValues(\React\Promise\DeferredResolver $deferredResolver) {
        $deferred = new \React\Promise\Deferred();
        $deferred->promise()->then(function($channels) use ($deferredResolver) {
            
            $values = new \SplObjectStorage;
            
            $deferredResolver->resolve($values);
        });
        $this->fetchModemStatusValue($deferred->resolver(), self::STATUS_TABLE, self::STATUS_COLUMN);
    }
```

Noticed the extra `\SplObjectStorage`, it's used to store the values. A similar foreach is used in this function but now we create `\PhuninNode\Value` instance for each channel and push that into the `\SplObjectStorage` instance.

```php
            foreach ($channels as $channel => $value) {
                $valueObject = new \PhuninNode\Value();
                $valueObject->setKey($channel);
                $valueObject->setValue($value);
                
                $values->attach($valueObject);
            }
```

All of this combined makes a PhuninNode plugin.

The plugin we just made is also on Github in the PhuninNodePlugins repo but more refined as there are more metrics to keep an eye on: [Cisco EPC3925 UpstreamPowerLevel](https://github.com/WyriHaximus/PhuninNodePlugins/blob/master/Cisco%20EPC3925/UpstreamPowerLevel.php)