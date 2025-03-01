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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Rule\Model\Action;
use CoreShop\Component\Rule\Model\Condition;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShippingRuleFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    private ?ContainerInterface $container;

    public function getVersion(): string
    {
        return '2.0';
    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void
    {
        if (!count($this->container->get('coreshop.repository.shipping_rule')->findAll())) {
            $defaultStore = $this->container->get('coreshop.repository.store')->findStandard();
            $currency = $defaultStore->getCurrency()->getId();

            $configuration = [
                [
                    'name' => 'demo1',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 0,
                                'maxAmount' => 15000,
                            ],
                        ],
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 500,
                                'currency' => $currency,
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'demo2',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 15000,
                                'maxAmount' => 200000,
                            ],
                        ],
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 1000,
                                'currency' => $currency,
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'demo3',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 200000,
                                'maxAmount' => 300000,
                            ],
                        ],
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 2000,
                                'currency' => $currency,
                            ],
                        ],
                    ],
                ],
            ];

            foreach ($configuration as $index => $config) {
                $rule = $this->container->get('coreshop.factory.shipping_rule')->createNew();
                $rule->setName($config['name']);
                $rule->setActive(true);

                foreach ($config['conditions'] as $cond) {
                    $condition = new Condition();
                    $condition->setType($cond['type']);
                    $condition->setConfiguration($cond['config']);

                    $rule->addCondition($condition);
                }

                foreach ($config['actions'] as $act) {
                    $action = new Action();
                    $action->setType($act['type']);
                    $action->setConfiguration($act['config']);

                    $rule->addAction($action);
                }

                $manager->persist($rule);

                $this->setReference('shippingRule' . $index, $rule);
            }

            $manager->flush();
        }
    }
}
