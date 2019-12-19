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
class Configuration implements SingletonInterface
{
    /**
     * @var array[][]
     */
    protected $tableConfigs = [];

    /**
     * @return Configuration
     */
    public static function getInstance(): Configuration
    {
        /** @var Configuration $config */
        $config = GeneralUtility::makeInstance(__CLASS__);
        return $config;
    }

    /**
     * @param string[] $tables
     * @param string $agent
     * @param array $agentConfiguration
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

    /**
     * @param string $table
     * @return bool
     */
    public function isConfigured(string $table): bool
    {
        return isset($this->tableConfigs[$table]);
    }

    /**
     * @param string $table
     * @return array
     */
    public function getAgentsForTable(string $table): array
    {
        return $this->tableConfigs[$table];
    }
}
