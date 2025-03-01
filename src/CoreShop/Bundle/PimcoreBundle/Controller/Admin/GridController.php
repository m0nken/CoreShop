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

namespace CoreShop\Bundle\PimcoreBundle\Controller\Admin;

use CoreShop\Component\Pimcore\DataObject\Grid\GridActionInterface;
use CoreShop\Component\Pimcore\DataObject\Grid\GridFilterInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GridController extends AdminController
{
    public function getGridFiltersAction(string $listType): Response
    {
        $gridFilterRepository = $this->get('coreshop.registry.grid.filter');
        $trans = $this->get('translator');

        $services = [];
        /** @var GridFilterInterface $service */
        foreach ($gridFilterRepository->all() as $id => $service) {
            if ($service->supports($listType) !== true) {
                continue;
            }

            $services[] = [
                'id' => $id,
                'name' => $trans->trans($service->getName(), [], 'admin'),
            ];
        }

        return $this->json($services);
    }

    public function getGridActionsAction(string $listType): Response
    {
        $gridActionRepository = $this->get('coreshop.registry.grid.action');
        $trans = $this->get('translator');

        $services = [];
        /** @var GridActionInterface $service */
        foreach ($gridActionRepository->all() as $id => $service) {
            if ($service->supports($listType) !== true) {
                continue;
            }

            $services[] = [
                'id' => $id,
                'name' => $trans->trans($service->getName(), [], 'admin'),
            ];
        }

        return $this->json($services);
    }

    public function applyGridAction(Request $request): Response
    {
        $requestedIds = $request->request->get('ids');
        $actionId = (string)$request->request->get('actionId');

        if (is_string($requestedIds)) {
            $requestedIds = json_decode($requestedIds);
        }

        $gridActionRepository = $this->get('coreshop.registry.grid.action');

        $success = true;

        if (!$gridActionRepository->has($actionId)) {
            $success = false;
            $message = sprintf('Action Service %s not found.', $actionId);
        } else {
            try {
                /** @var GridActionInterface $actionService */
                $actionService = $gridActionRepository->get($actionId);
                $message = $actionService->apply($requestedIds);
            } catch (\Exception $e) {
                $success = false;
                $message = $e->getMessage();
            }
        }

        return $this->json([
            'success' => $success,
            'message' => $message,
        ]);
    }
}
