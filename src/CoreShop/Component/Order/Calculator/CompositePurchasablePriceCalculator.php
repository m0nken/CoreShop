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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Exception\NoPurchasablePriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasablePriceCalculator implements PurchasablePriceCalculatorInterface
{
    public function __construct(protected PrioritizedServiceRegistryInterface $calculators)
    {
    }

    public function getPrice(PurchasableInterface $purchasable, array $context, bool $includingDiscounts = false): int
    {
        $price = null;

        /**
         * @var PurchasablePriceCalculatorInterface $calculator
         */
        foreach ($this->calculators->all() as $calculator) {
            try {
                $actionPrice = $calculator->getPrice($purchasable, $context, $includingDiscounts);
                $price = $actionPrice;
            } catch (NoPurchasablePriceFoundException) {
            }
        }

        if (null === $price) {
            throw new NoPurchasablePriceFoundException(__CLASS__);
        }

        return $price;
    }
}
