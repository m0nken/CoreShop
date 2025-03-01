<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Service;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\InvalidArgumentException;

final class IndexUpdaterService implements IndexUpdaterServiceInterface
{
    public function __construct(private RepositoryInterface $indexRepository, private ServiceRegistryInterface $workerServiceRegistry)
    {
    }

    public function updateIndices(IndexableInterface $subject, bool $isVersionChange = false): void
    {
        $this->operationOnIndex($subject, 'update', $isVersionChange);
    }

    public function removeIndices(IndexableInterface $subject): void
    {
        $this->operationOnIndex($subject, 'remove');
    }

    private function operationOnIndex(IndexableInterface $subject, string $operation = 'update', bool $isVersionChange = false): void
    {
        $indices = $this->indexRepository->findAll();

        foreach ($indices as $index) {
            if (!$index instanceof IndexInterface) {
                continue;
            }

            if (!$this->isEligible($index, $subject)) {
                continue;
            }

            //Don't store version changes into the index!
            if ($isVersionChange && !$index->getIndexLastVersion()) {
                continue;
            }

            $worker = $index->getWorker();

            if (!$this->workerServiceRegistry->has($worker)) {
                throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
            }

            /**
             * @var WorkerInterface $worker
             */
            $worker = $this->workerServiceRegistry->get($worker);

            if ($operation === 'update') {
                $worker->updateIndex($index, $subject);
            } else {
                $worker->deleteFromIndex($index, $subject);
            }
        }
    }

    private function isEligible(IndexInterface $index, IndexableInterface $subject): bool
    {
        if (!$subject instanceof Concrete) {
            return false;
        }

        if ($subject->getClass()->getName() !== $index->getClass()) {
            return false;
        }

        return true;
    }
}
