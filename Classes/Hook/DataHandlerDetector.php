<?php

namespace Pluswerk\CacheAutomation\Hook;

use Pluswerk\CacheAutomation\Agents\AgentInterface;
use Pluswerk\CacheAutomation\Service\Configuration;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Service\CacheService;
use RuntimeException;

#[Autoconfigure(public: true)]
final readonly class DataHandlerDetector implements SingletonInterface
{
    public function __construct(private Configuration $configuration, private CacheService $cacheService)
    {
    }

    /**
     * @param string $status The status of the record
     * @param string $table Database table name
     * @param int|string $id The uid of the record or something like "NEW59785a1ec52" if the record is new
     * @param array<mixed> $changedFields Field value map of the changed fields
     * @param DataHandler $dataHandler Reference back to the DataHandler
     * @throws RuntimeException
     */
    // @phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function processDatamap_afterDatabaseOperations(/** @noinspection PhpUnusedParameterInspection */ string $status, string $table, int|string $id, array $changedFields, DataHandler $dataHandler): void
    {
        if (!MathUtility::canBeInterpretedAsInteger($id)) {
            return;
        }

        $this->clearFor($table);
    }

    public function clearFor(string $table): void
    {
        $expiredPages = [];
        if ($this->configuration->isConfigured($table)) {
            $agentConfigurations = $this->configuration->getAgentsForTable($table);
            foreach ($agentConfigurations as $agentConfiguration) {
                $agentClass = $agentConfiguration['agent'];
                /** @var class-string $agentClass */
                $agent = GeneralUtility::makeInstance($agentClass);
                if ($agent instanceof AgentInterface) {
                    $expiredPages = $agent->getExpiredPages($table, $agentConfiguration['agentConfiguration']);
                } else {
                    throw new RuntimeException(
                        'Agent "' . $agentConfiguration['agent'] . '" must implement \Pluswerk\CacheAutomation\Agent\AgentInterface',
                        1500979398
                    );
                }
            }
        }

        if (count($expiredPages) !== 0) {
            $this->cacheService->clearPageCache(array_unique($expiredPages));
        }
    }
}
