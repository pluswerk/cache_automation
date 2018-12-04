<?php

namespace Pluswerk\CacheAutomation\Agents;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\QueryGenerator;

class PageMovedAgent extends AbstractAgent
{
    public function getExpiredPages(string $table, $uid, array $agentConfiguration, array $changedFields): array
    {
        $pagesUidList = [];
        if ($table === 'pages') {
            if ($this->pageMovedInTree($changedFields)) {
                $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
                $pages = $queryGenerator->getTreeList($uid, 99, 0, 1);
                $pagesUidList = array_merge($pagesUidList, explode(',', $pages));
            }
        }

        return $pagesUidList;
    }

    /**
     * Returns true, if the page was moved via the page tree
     *
     * @param array $changedFields
     * @return bool
     */
    protected function pageMovedInTree(array $changedFields): bool
    {
        return array_keys($changedFields) === ['uid'];
    }
}
