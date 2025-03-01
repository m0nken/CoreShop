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

namespace CoreShop\Bundle\ShippingBundle\Form\Type\Rule\Common;

use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleChoiceType;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ShippingRuleConfigurationType extends AbstractType
{
    /**
     * @param string[] $validationGroups
     */
    public function __construct(protected array $validationGroups)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shippingRule', ShippingRuleChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                ],
            ]);

        $builder->get('shippingRule')->addModelTransformer(new CallbackTransformer(
            function (mixed $shippingRule) {
                if ($shippingRule instanceof ShippingRuleInterface) {
                    return $shippingRule->getId();
                }

                return null;
            },
            function (mixed $shippingRule) {
                if ($shippingRule instanceof ShippingRuleInterface) {
                    return $shippingRule->getId();
                }

                return null;
            }
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_shipping_rule_condition_shipping_rule';
    }
}
