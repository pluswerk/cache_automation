<?php

/***
 * This file is part of an +Pluswerk AG Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2017 Markus Hölzle <markus.hoelzle@pluswerk.ag>, +Pluswerk AG
 ***/

namespace Pluswerk\CacheAutomation\Agents;

use Doctrine\DBAL\FetchMode;

/**
 * Class SimplePluginAgent
 *
 * @author Markus Hölzle <markus.hoelzle@pluswerk.ag>
 * @package Pluswerk\CacheAutomation\Agents
 */
final class SimplePluginAgent extends AbstractAgent
{
    /**
     * Clear caches from pages which contain the configured plugins.
     *
     * Configuration:
     * [
     *   'pluginKeys' => ['my_plugin_key1', 'my_plugin_key2'],
     * ]
     *
     * @param string $table
     * @param int|string $uid
     * @param array $agentConfiguration
     * @param array $changedFields
     * @return int[]
     */
    public function getExpiredPages(string $table, $uid, array $agentConfiguration, array $changedFields): array
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
