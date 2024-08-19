<?php

namespace Pluswerk\CacheAutomation\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class Configuration implements SingletonInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $tableConfigs = [];

    /**
     * @deprecated use GeneralUtility::makeInstance(Configuration::class) instead
     */
    public static function getInstance(): Configuration
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * @param string[] $tables
     * @param array<mixed> $agentConfiguration
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

    /**
     * @return array<mixed>
     */
    public function getAgentsForTable(string $table): array
    {
        return $this->tableConfigs[$table];
    }
}
