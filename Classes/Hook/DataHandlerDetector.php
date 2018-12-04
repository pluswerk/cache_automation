<?php
namespace Pluswerk\CacheAutomation\Hook;

/***
 * This file is part of an +Pluswerk AG Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2017 Markus Hölzle <markus.hoelzle@pluswerk.ag>, +Pluswerk AG
 ***/

use Pluswerk\CacheAutomation\Agents\AgentInterface;
use Pluswerk\CacheAutomation\Service\Configuration;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\CacheService;

/**
 * Class DataHandlerDetector
 *
 * @author Markus Hölzle <markus.hoelzle@pluswerk.ag>
 * @package Pluswerk\CacheAutomation\Hook
 */
class DataHandlerDetector implements SingletonInterface
{

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * DataHandlerHook constructor.
     */
    public function __construct()
    {
        $this->configuration = Configuration::getInstance();
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->cacheService = $objectManager->get(CacheService::class);
    }

    // @codingStandardsIgnoreStart
    /**
     * @param string $status The status of the record
     * @param string $table Database table name
     * @param int|string $id The uid of the record or something like "NEW59785a1ec52" if the record is new
     * @param array $changedFields Field value map of the changed fields
     * @param DataHandler $dataHandler Reference back to the DataHandler
     * @throws \Exception
     */
    public function processDatamap_afterDatabaseOperations($status, string $table, $id, array $changedFields, DataHandler $dataHandler)
    {
        $this->clearCachedPagesFoundByAgents($table, $id, $changedFields);

    }

    /**
     * @param string $status The status of the record
     * @param string $table Database table name
     * @param int|string $id The uid of the record or something like "NEW59785a1ec52" if the record is new
     * @param int $pageIdentifier The id of the page
     * @throws \Exception
     */
    public function processCmdmap_postProcess($status, $table, $id, $pageIdentifier)
    {
        $this->clearCachedPagesFoundByAgents($table, $id, ['uid' => $pageIdentifier]);

    }

    /**
     * @param string $table
     * @param $id
     * @param array $changedFields of an array containing just the uid of a page
     * @throws \Exception
     */
    protected function clearCachedPagesFoundByAgents(string $table, $id, array $changedFields)
    {
// @codingStandardsIgnoreEnd
        $expiredPages = [];
        if ($this->configuration->isConfigured($table)) {
            $agentConfigurations = $this->configuration->getAgentsForTable($table);
            foreach ($agentConfigurations as $agentConfiguration) {
                /** @var AgentInterface $agent */
                $agent = GeneralUtility::makeInstance($agentConfiguration['agent']);
                if ($agent instanceof AgentInterface) {
                    $expiredPages = $agent->getExpiredPages($table, $id, $agentConfiguration['agentConfiguration'], $changedFields);
                } else {
                    throw new \Exception('Agent "' . $agentConfiguration['agent'] . '" must implement \Pluswerk\CacheAutomation\Agent\AgentInterface', 1500979398);
                }
            }
        }

        if (count($expiredPages) !== 0) {
            $expiredPages = array_unique($expiredPages);
            // TODO: use new API in TYPO3 V9
            $extensionConfiguration = $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cache_automation']);
            $arrayChunks = array_chunk($expiredPages, $extensionConfiguration['numberOfCachedPagesToClear']);
            foreach ($arrayChunks as $singleChunk) {
                $this->cacheService->clearPageCache($singleChunk);
            }
        }
    }
}
