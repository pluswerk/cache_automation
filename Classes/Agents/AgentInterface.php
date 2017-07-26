<?php
namespace Pluswerk\CacheAutomation\Agents;

/***
 * This file is part of an +Pluswerk AG Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2017 Markus Hölzle <markus.hoelzle@pluswerk.ag>, +Pluswerk AG
 ***/

/**
 * Interface AgentInterface
 *
 * @author Markus Hölzle <markus.hoelzle@pluswerk.ag>
 * @package Pluswerk\CacheAutomation\Agents
 */
interface AgentInterface
{
    /**
     * @param string $table
     * @param int $uid
     * @param array $agentConfiguration
     * @param array $changedFields
     * @return int[]
     */
    public function getExpiredPages(string $table, int $uid, array $agentConfiguration, array $changedFields): array;
}
