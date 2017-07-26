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

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PageRootlineAgent
 *
 * @author Markus Hölzle <markus.hoelzle@pluswerk.ag>
 * @package Pluswerk\CacheAutomation\Agents
 */
class PageRootlineAgent extends AbstractAgent
{
    /**
     * Clear caches from all pages which has the configured pages in "rootline".
     *
     * Minimal configuration:
     * [
     *   'rootPages' => [42, 316],
     * ]
     *
     * Full configuration:
     * [
     *   'rootPages' => [42, 316],
     *   'depth' => 99,
     *   'begin' => 0,
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
        $pagesUidList = [];
        $depth = isset($agentConfiguration['depth']) ? $agentConfiguration['depth'] : 99;
        $begin = isset($agentConfiguration['begin']) ? $agentConfiguration['begin'] : 0;
        foreach ($agentConfiguration['rootPages'] as $rootPage) {
            /** @var QueryGenerator $queryGenerator */
            $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
            $pages = $queryGenerator->getTreeList($rootPage, $depth, $begin, 1);
            $pagesUidList = array_merge($pagesUidList, explode(',', $pages));
        }
        return $pagesUidList;
    }
}
