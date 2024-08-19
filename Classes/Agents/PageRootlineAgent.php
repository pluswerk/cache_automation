<?php

declare(strict_types=1);

namespace Pluswerk\CacheAutomation\Agents;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;

final readonly class PageRootlineAgent implements AgentInterface
{
    public function __construct(protected ConnectionPool $connectionPool)
    {
    }

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
     * @param array{rootPages: int[], depth?: int, begin?: int} $agentConfiguration
     * @return int[]
     */
    public function getExpiredPages(string $table, array $agentConfiguration): array
    {
        $pagesUidList = [];
        $depth = $agentConfiguration['depth'] ?? 99;
        $begin = $agentConfiguration['begin'] ?? 0;
        foreach ($agentConfiguration['rootPages'] as $rootPage) {
            $pages = $this->getTreeList($rootPage, $depth, $begin);
            $pagesUidList = [...$pagesUidList, ...$pages];
        }

        return array_unique($pagesUidList);
    }

    /**
     * Recursively fetch all descendants of a given page
     *
     * @param int $id uid of the page
     * @return int[]
     */
    public function getTreeList(int $id, int $depth, int $begin): array
    {
        $theList = [];
        if ($begin === 0) {
            $theList[] = $id;
        }

        if (!$id || $depth <= 0) {
            return $theList;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $rows = $queryBuilder
            ->select('uid')
            ->from('pages')
            ->where($queryBuilder->expr()->eq('pid', $id))
            ->andWhere($queryBuilder->expr()->eq('sys_language_uid', 0))
            ->orderBy('uid')
            ->executeQuery()
            ->fetchAllAssociative();
        foreach ($rows as $row) {
            if ($begin <= 0) {
                $theList[] = (int)$row['uid'];
            }

            if ($depth > 1) {
                $theSubList = $this->getTreeList($row['uid'], $depth - 1, $begin - 1);

                $theList = [...$theList, ...$theSubList];
            }
        }

        return $theList;
    }
}
