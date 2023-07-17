<?php

/***
 * This file is part of an +Pluswerk AG Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2017 Markus Hölzle <markus.hoelzle@pluswerk.ag>, +Pluswerk AG
 ***/

namespace Pluswerk\CacheAutomation\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Configuration
 *
 * @author Markus Hölzle <markus.hoelzle@pluswerk.ag>
 * @package Pluswerk\CacheAutomation\Service
 */
final class Configuration implements SingletonInterface
{
    /**
     * @var array[][]
     */
    private array $tableConfigs = [];

    public static function getInstance(): Configuration
    {
        /** @var Configuration $config */
        $config = GeneralUtility::makeInstance(self::class);
        return $config;
    }

    /**
     * @param string[] $tables
     */
    public function addAgentForTables(array $tables, string $agent, array $agentConfiguration = []): void
    {
        foreach ($tables as $table) {
            if (!isset($this->tableConfigs[$table])) {
                $this->tableConfigs[$table] = [];
            }

            $this->tableConfigs[$table][] = [
                'agent' => $agent,
                'agentConfiguration' => $agentConfiguration,
            ];
        }
    }

    public function isConfigured(string $table): bool
    {
        return isset($this->tableConfigs[$table]);
    }

    public function getAgentsForTable(string $table): array
    {
        return $this->tableConfigs[$table];
    }
}
