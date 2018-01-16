<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use Pimcore\Model\DataObject\CoreShopOrder\PaymentData;

class Order extends Sale implements OrderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTotalPayed()
    {
        $totalPayed = 0;

        foreach ($this->getPayments() as $payment) {
            if ($payment->getTotalAmount()) {
                $totalPayed += $payment->getTotalAmount();
            }
        }

        return $totalPayed;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPayed()
    {
        return $this->getTotal() === $this->getTotalPayed();
    }

    /**
     * {@inheritdoc}
     */
    public function getSaleLanguage()
    {
        return $this->getOrderLanguage();
    }

    /**
     * {@inheritdoc}
     */
    public function setSaleLanguage($saleLanguage)
    {
        return $this->setOrderLanguage($saleLanguage);
    }

    /**
     * {@inheritdoc}
     */
    public function getSaleDate()
    {
        return $this->getOrderDate();
    }

    /**
     * {@inheritdoc}
     */
    public function setSaleDate($saleDate)
    {
        return $this->setOrderDate($saleDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getSaleNumber()
    {
        return $this->getOrderNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function setSaleNumber($saleNumber)
    {
        return $this->setOrderNumber($saleNumber);
    }

    /**
     * @return string
     */
    public function getOrderState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $orderState
     */
    public function setOrderState($orderState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return string
     */
    public function getShippingState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $shippingState
     */
    public function setShippingState($shippingState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return string
     */
    public function getInvoiceState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $invoiceState
     */
    public function setInvoiceState($invoiceState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }


    /**
     * @return string
     */
    public function getPaymentState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $paymentState
     */
    public function setPaymentState($paymentState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderLanguage()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderLanguage($orderLanguage)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderDate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderDate($orderDate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderNumber()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderNumber($orderNumber)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return PaymentRepositoryInterface
     */
    private function getPaymentRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.payment');
    }

    /**
     * @return OrderInvoiceRepositoryInterface
     */
    private function getOrderInvoiceRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.order_invoice');
    }

    /**
     * @return OrderShipmentRepositoryInterface
     */
    private function getOrderShipmentRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.order_shipment');
    }

    /**
     * {@inheritdoc}
     */
    public function getPayments()
    {
        return $this->getPaymentRepository()->findForOrderId($this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getShipments()
    {
        return $this->getOrderShipmentRepository()->getDocuments($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoices()
    {
        return $this->getOrderInvoiceRepository()->getDocuments($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentProvider()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentProvider($paymentProvider)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
