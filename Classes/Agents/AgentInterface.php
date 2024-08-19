<?php

declare(strict_types=1);

namespace Pluswerk\CacheAutomation\Agents;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
interface AgentInterface
{
    /**
     * @param string $table Database table name
     * @param array<mixed> $agentConfiguration The agent configuration array
     * @return int[]
     */
    public function getExpiredPages(string $table, array $agentConfiguration): array;
}
