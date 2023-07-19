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

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Connection;

/**
 * Class PageRootlineAgent
 *
 * @author Markus Hölzle <markus.hoelzle@pluswerk.ag>
 * @package Pluswerk\CacheAutomation\Agents
 */
final class PageRootlineAgent extends AbstractAgent
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
     * @param array<mixed> $agentConfiguration
     * @param array<mixed> $changedFields
     * @return int[]
     */
    public function getExpiredPages(string $table, $uid, array $agentConfiguration, array $changedFields): array
    {
        $pagesUidList = '';
        $depth = $agentConfiguration['depth'] ?? 99;
        $begin = $agentConfiguration['begin'] ?? 0;
        foreach ($agentConfiguration['rootPages'] as &$rootPage) {
            $pages = $this->getTreeList($rootPage, $depth, $begin, '1');
            if ($pagesUidList !== '') {
                $pagesUidList .= ',';
            }

            $pagesUidList .= $pages;
        }

        return array_flip(array_flip(array_map('intval', explode(',', $pagesUidList))));
    }

    /**
     * Copied from deprecated QueryGenerator class
     * Recursively fetch all descendants of a given page
     *
     * @param int $id uid of the page
     * @param int $depth
     * @param int $begin
     * @param string $permClause
     * @return string comma separated list of descendant pages
     */
    public function getTreeList($id, $depth, $begin = 0, $permClause = '')
    {
        $depth = (int)$depth;
        $begin = (int)$begin;
        $id = (int)$id;
        if ($id < 0) {
            $id = abs($id);
        }

        if ($begin == 0) {
            $theList = (string)$id;
        } else {
            $theList = '';
        }

        if ($id && $depth > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $queryBuilder->select('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT)),
                    $queryBuilder->expr()->eq('sys_language_uid', 0)
                )
                ->orderBy('uid');
            if ($permClause !== '') {
                $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($permClause));
            }

            $statement = $queryBuilder->executeQuery();
            while ($row = $statement->fetchAssociative()) {
                if ($begin <= 0) {
                    $theList .= ',' . $row['uid'];
                }

                if ($depth > 1) {
                    $theSubList = $this->getTreeList($row['uid'], $depth - 1, $begin - 1, $permClause);
                    if (!empty($theList) && !empty($theSubList) && ($theSubList[0] !== ',')) {
                        $theList .= ',';
                    }

                    $theList .= $theSubList;
                }
            }
        }

        return $theList;
    }
}
