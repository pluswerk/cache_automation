<?php

namespace Pluswerk\CacheAutomation\Agents;

use TYPO3\CMS\Core\Database\ConnectionPool;

final readonly class SimplePluginAgent implements AgentInterface
{
    public function __construct(protected ConnectionPool $connectionPool)
    {
    }

    /**
     * Clear caches from pages which contain the configured plugins.
     *
     * Configuration:
     * [
     *   'pluginKeys' => ['my_plugin_key1', 'my_plugin_key2'],
     * ]
     *
     * @param array{pluginKeys: string[]} $agentConfiguration
     * @return int[]
     */
    public function getExpiredPages(string $table, array $agentConfiguration): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $pluginKeys = [];
        foreach ($agentConfiguration['pluginKeys'] as $pluginKey) {
            $pluginKeys[] = $queryBuilder->createNamedParameter($pluginKey);
        }

        return $queryBuilder
            ->select('pid')
            ->from('tt_content')
            ->where($queryBuilder->expr()->in('list_type', $pluginKeys))
            ->executeQuery()
            ->fetchFirstColumn();
    }
}
