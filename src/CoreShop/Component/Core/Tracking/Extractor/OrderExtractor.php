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

namespace CoreShop\Component\Core\Tracking\Extractor;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class OrderExtractor implements TrackingExtractorInterface
{
    public function __construct(protected TrackingExtractorInterface $extractor, protected int $decimalFactor, protected int $decimalPrecision)
    {
    }

    public function supports($object): bool
    {
        return $object instanceof OrderInterface;
    }

    public function updateMetadata($object, $data = []): array
    {
        $items = [];

        foreach ($object->getItems() as $item) {
            $items[] = $this->extractor->updateMetadata($item);
        }

        return array_merge(
            $data,
            [
                'id' => $object->getId(),
                'affiliation' => $this->parseAmount($object->getTotal()),
                'total' => $this->parseAmount($object->getTotal()),
                'subtotal' => $this->parseAmount($object->getSubtotal()),
                'totalTax' => $this->parseAmount($object->getTotalTax()),
                'shipping' => $this->parseAmount($object->getAdjustmentsTotal(AdjustmentInterface::SHIPPING)),
                'discount' => $this->parseAmount($object->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE)),
                'currency' => $object->getCurrency()->getIsoCode(),
                'items' => $items,
            ]
        );
    }

    protected function parseAmount(int $amount): int
    {
        return (int)round((round($amount / $this->decimalFactor, $this->decimalPrecision)), 0);
    }
}
