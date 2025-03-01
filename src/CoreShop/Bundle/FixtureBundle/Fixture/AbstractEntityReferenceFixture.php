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

namespace CoreShop\Bundle\FixtureBundle\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;

abstract class AbstractEntityReferenceFixture extends AbstractFixture
{
    /**
     * @psalm-param class-string $className
     */
    protected function getObjectReferences(ObjectManager $objectManager, string $className): array
    {
        $identifier = $objectManager->getClassMetadata($className)->getIdentifier();
        $idField = reset($identifier);

        /** @var EntityRepository $objectRepository */
        $objectRepository = $objectManager->getRepository($className);

        $idsResult = $objectRepository
            ->createQueryBuilder('t')
            ->select('t.' . $idField)
            ->getQuery()
            ->getArrayResult();

        $ids = [];
        foreach ($idsResult as $result) {
            $ids[] = $result[$idField];
        }

        return $this->getObjectReferencesByIds($objectManager, $className, $ids);
    }

    /**
     * @psalm-param class-string $className
     */
    protected function getObjectReferencesByIds(ObjectManager $objectManager, string $className, array $ids): array
    {
        $entities = [];

        foreach ($ids as $id) {
            if ($objectManager instanceof EntityManager) {
                $entities[] = $objectManager->getReference($className, $id);
            }
        }

        return $entities;
    }
}
