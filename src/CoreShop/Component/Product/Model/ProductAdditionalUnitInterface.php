<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProductAdditionalUnitInterface extends ResourceInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * {@inheritdoc}
     */
    public function getPrice();

    /**
     * {@inheritdoc}
     */
    public function setPrice(int $price);

    /**
     * @return ProductUnitInterface
     */
    public function getUnit();

    /**
     * @param ProductUnitInterface $unit
     */
    public function setUnit(ProductUnitInterface $unit);

    /**
     * @return int
     */
    public function getPrecision();

    /**
     * @param int $precision
     */
    public function setPrecision(int $precision);

    /**
     * @return float
     */
    public function getConversionRate();

    /**
     * @param float $conversionRate
     */
    public function setConversionRate(float $conversionRate);
}
