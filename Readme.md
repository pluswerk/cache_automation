[![Packagist Release](https://img.shields.io/packagist/v/pluswerk/cache-automation.svg?style=flat-square)](https://packagist.org/packages/pluswerk/cache-automation)
[![Travis](https://img.shields.io/travis/pluswerk/cache_automation.svg?style=flat-square)](https://travis-ci.org/pluswerk/cache_automation)
[![GitHub License](https://img.shields.io/github/license/pluswerk/cache_automation.svg?style=flat-square)](https://github.com/pluswerk/cache_automation/blob/master/LICENSE.txt)
[![Code Climate](https://img.shields.io/codeclimate/github/pluswerk/cache_automation.svg?style=flat-square)](https://codeclimate.com/github/pluswerk/cache_automation)

# TYPO3 Extension: Cache Automation

This TYPO3 extension clear caches automated in the right moment. This happens by some magic configuration of your extension.

Example:

With this TYPO3 extension you can cache for example an extbase "list and show" plugin. If a database record is updated the cache of all pages containing this plugin will be flushed.

You can write your own magic cache agent.


Requires TYPO3 8.7 up to TYPO3 9

Issue tracking: [GitHub: TYPO3 Cache Automation](https://github.com/pluswerk/cache_automation/issues)

Packagist: [pluswerk/cache-automation](https://packagist.org/packages/pluswerk/cache-automation)


## Installation

1.  Install the TYPO3 extension via composer

> Composer installation:
>
> ```bash
> composer require pluswerk/cache-automation
> ```


## Configuration

### Configure cache agent

Configure a cache agent for your extension in your `ext_localconf.php`.

> A cache agent is triggered, if a database record of the given tables has changed.

Example:

```php
\Pluswerk\CacheAutomation\Service\Configuration::getInstance()->addAgentForTables(
    ['tx_news_domain_model_news'], // database table name
    \Pluswerk\CacheAutomation\Agents\SimplePluginAgent::class, // cache agent
    [
        // cache agent configuration
        'pluginKeys' => ['news_pi1'],
    ]
);
```

### Available cache agents

#### SimplePluginAgent

This agent flush the cache of all pages which have a content element with the given plugin keys.

```php
\Pluswerk\CacheAutomation\Service\Configuration::getInstance()->addAgentForTables(
    ['tx_news_domain_model_news'],
    \Pluswerk\CacheAutomation\Agents\SimplePluginAgent::class,
    [
        'pluginKeys' => ['news_pi1'],
    ]
);
```

#### PageRootlineAgent

This agent flush the cache of all pages which are in the TYPO3 "rootline" of the given pages.

```php
\Pluswerk\CacheAutomation\Service\Configuration::getInstance()->addAgentForTables(
    ['tx_news_domain_model_news'],
    \Pluswerk\CacheAutomation\Agents\PageRootlineAgent::class,
    [
        'rootPages' => [42, 316],
        'depth' => 99, // optional
        'begin' => 0, // optional
    ]
);
```

## Use your own cache agent

You can simply use your own cache agent. It has to implement `\Pluswerk\CacheAutomation\Agents\AgentInterface`:

```php
class MyMagicCacheAgent implements \Pluswerk\CacheAutomation\Agents\AgentInterface
{
    public function getExpiredPages(string $table, int $uid, array $agentConfiguration, array $changedFields): array
    {
        // doe some magic here and return all page uid's which caches should be flushed
        return [42, 316];
    }
}
```
