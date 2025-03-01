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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderDocumentPrintController extends FrontendController
{
    public function invoiceAction(Request $request, OrderDocumentInterface $document, OrderInterface $order): Response
    {
        return $this->render('@CoreShopOrder/OrderDocumentPrint/invoice.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType(),
        ]);
    }

    public function shipmentAction(Request $request, OrderDocumentInterface $document, OrderInterface $order): Response
    {
        return $this->render('@CoreShopOrder/OrderDocumentPrint/shipment.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType(),
        ]);
    }

    public function headerAction(Request $request, OrderDocumentInterface $document, OrderInterface $order): Response
    {
        return $this->render('@CoreShopOrder/OrderDocumentPrint/header.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType(),
        ]);
    }

    public function footerAction(Request $request, OrderDocumentInterface $document, OrderInterface $order): Response
    {
        return $this->render('@CoreShopOrder/OrderDocumentPrint/footer.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType(),
        ]);
    }
}
