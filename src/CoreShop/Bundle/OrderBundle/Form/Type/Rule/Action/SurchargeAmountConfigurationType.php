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

namespace CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class SurchargeAmountConfigurationType extends AbstractType
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
            ->add('amount', MoneyType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                    new Type(['type' => 'numeric', 'groups' => $this->validationGroups]),
                    new GreaterThan(['value' => 0, 'groups' => $this->validationGroups]),
                ],
            ])
            ->add('gross', CheckboxType::class, [
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                ],
            ]);

        $builder->get('currency')->addModelTransformer(new CallbackTransformer(
            function (mixed $currency) {
                if ($currency instanceof CurrencyInterface) {
                    return $currency->getId();
                }

                return null;
            },
            function (mixed $currency) {
                if ($currency instanceof CurrencyInterface) {
                    return $currency->getId();
                }

                return null;
            }
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_cart_price_rule_action_surcharge_amount';
    }
}
