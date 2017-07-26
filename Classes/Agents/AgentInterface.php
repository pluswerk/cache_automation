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
     * @param string $table Database table name
     * @param int|string $uid The uid of the record or something like "NEW59785a1ec52" if the record is new
     * @param array $agentConfiguration The agent configuration array
     * @param array $changedFields Field value map of the changed fields
     * @return int[]
     */
    public function getExpiredPages(string $table, $uid, array $agentConfiguration, array $changedFields): array;
}
