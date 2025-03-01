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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ProductQuantityPriceRulesActionPass extends RegisterSimpleRegistryTypePass
{
    public const PRODUCT_QUANTITY_PRICE_RULE_ACTION_TAG = 'coreshop.product_quantity_price_rules.action';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.product_quantity_price_rules.actions',
            'coreshop.product_quantity_price_rules.actions',
            self::PRODUCT_QUANTITY_PRICE_RULE_ACTION_TAG
        );
    }
}
