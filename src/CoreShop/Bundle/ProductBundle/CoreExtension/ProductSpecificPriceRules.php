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

namespace CoreShop\Bundle\ProductBundle\CoreExtension;

use CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRuleType;
use CoreShop\Bundle\ResourceBundle\CoreExtension\TempEntityManagerTrait;
use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityMerger;
use CoreShop\Bundle\ResourceBundle\Pimcore\CacheMarshallerInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use CoreShop\Component\Resource\Factory\RepositoryFactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\LazyLoadedFieldsInterface;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class ProductSpecificPriceRules extends Data implements
    Data\CustomResourcePersistingInterface,
    Data\CustomVersionMarshalInterface,
    Data\CustomRecyclingMarshalInterface,
    Data\CustomDataCopyInterface,
    CacheMarshallerInterface
{
    use TempEntityManagerTrait;

    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopProductSpecificPriceRules';

    /**
     * @var int
     */
    public $height;

    public function getParameterTypeDeclaration(): ?string
    {
        return 'array';
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return 'array';
    }

    public function getPhpdocInputType(): ?string
    {
        return 'array';
    }

    public function getPhpdocReturnType(): ?string
    {
        return 'array';
    }

    /**
     * @param mixed $object
     *
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function preGetData($object)
    {
        Assert::isInstanceOf($object, ProductInterface::class);

        if (!$object instanceof Concrete) {
            return [];
        }

        $data = $object->getObjectVar($this->getName());

        if (!$object->isLazyKeyLoaded($this->getName())) {
            $data = $this->load($object, ['force' => true]);

            $setter = 'set' . ucfirst($this->getName());
            if (method_exists($object, $setter)) {
                $object->$setter($data);
            }
        }

        return $data;
    }

    public function createDataCopy(Concrete $object, $data)
    {
        if (!is_array($data)) {
            return [];
        }

        if (!$object instanceof ProductInterface) {
            return [];
        }

        $newPriceRules = [];

        foreach ($data as $priceRule) {
            if (!$priceRule instanceof ProductSpecificPriceRuleInterface) {
                continue;
            }

            $newPriceRule = clone $priceRule;

            $reflectionClass = new \ReflectionClass($newPriceRule);
            $property = $reflectionClass->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($newPriceRule, null);

            $property = $reflectionClass->getProperty('product');
            $property->setAccessible(true);
            $property->setValue($newPriceRule, null);

            $property = $reflectionClass->getProperty('conditions');
            $property->setAccessible(true);
            $property->setValue($newPriceRule, new ArrayCollection());

            $property = $reflectionClass->getProperty('actions');
            $property->setAccessible(true);
            $property->setValue($newPriceRule, new ArrayCollection());

            foreach ($priceRule->getConditions() as $condition) {
                $newCondition = clone $condition;

                $reflectionClass = new \ReflectionClass($newCondition);
                $property = $reflectionClass->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($newCondition, null);

                $newPriceRule->addCondition($newCondition);
            }

            foreach ($priceRule->getActions() as $action) {
                $newAction = clone $action;

                $reflectionClass = new \ReflectionClass($newAction);
                $property = $reflectionClass->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($newAction, null);

                $newPriceRule->addAction($newAction);
            }

            $newPriceRules[] = $newPriceRule;
        }

        return $newPriceRules;
    }

    /**
     * @param Concrete $object
     * @param mixed    $data
     * @param array    $params
     *
     * @return mixed
     */
    public function preSetData($object, $data, $params = [])
    {
        if ($object instanceof LazyLoadedFieldsInterface) {
            $object->markLazyKeyAsLoaded($this->getName());
        }

        return $data;
    }

    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    public function getDataFromResource($data, $object = null, $params = [])
    {
        return [];
    }

    public function marshalVersion($object, $data)
    {
        if (!is_array($data)) {
            return null;
        }

        $serialized = [];

        foreach ($data as $datum) {
            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Version']);

            $serialized[] = $this->getSerializer()->toArray($datum, $context);
        }

        return $serialized;
    }

    public function unmarshalVersion($object, $data)
    {
        if (!is_array($data)) {
            return null;
        }

        $entities = [];
        $tempEntityManager = $this->createTempEntityManager($this->getEntityManager());

        foreach ($data as $storeData) {
            if (!is_array($storeData)) {
                continue;
            }

            $context = DeserializationContext::create();
            $context->setGroups(['Version']);
            $context->setAttribute('em', $tempEntityManager);

            $data = $this->getSerializer()->fromArray($storeData, $this->getProductSpecificPriceRuleRepository()->getClassName(), $context);

            $entities[] = $data;
        }

        return $entities;
    }

    public function marshalRecycleData($object, $data)
    {
        return $this->marshalVersion($object, $data);
    }

    public function unmarshalRecycleData($object, $data)
    {
        return $this->unmarshalVersion($object, $data);
    }

    public function marshalForCache(Concrete $concrete, mixed $data): mixed
    {
        return $this->marshalVersion($concrete, $data);
    }

    public function unmarshalForCache(Concrete $concrete, mixed $data): mixed
    {
        return $this->unmarshalVersion($concrete, $data);
    }

    /**
     * @param array $data
     * @param null  $object
     * @param array $params
     *
     * @return string
     */
    public function getVersionPreview($data, $object = null, $params = [])
    {
        if (!is_array($data)) {
            return 'empty';
        }

        return sprintf('Rules: %s', count($data));
    }

    /**
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return array
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $result = [
            'actions' => array_keys($this->getConfigActions()),
            'conditions' => array_keys($this->getConfigConditions()),
            'rules' => [],
        ];

        if ($object instanceof ProductInterface) {
            $prices = $this->load($object, ['force' => true]);

            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Default', 'Detailed']);

            $serializedData = $this->getSerializer()->toArray($prices, $context);

            $result['rules'] = $serializedData;
        }

        return $result;
    }

    /**
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $prices = [];
        $errors = [];

        $tempEntityManager = $this->createTempEntityManager($this->getEntityManager());
        $specificPriceRuleRepository = $this->getProductSpecificPriceRuleRepositoryFactory()->createNewRepository($tempEntityManager);

        if ($data && $object instanceof Concrete) {
            foreach ($data as $dataRow) {
                $ruleId = isset($dataRow['id']) && is_numeric($dataRow['id']) ? $dataRow['id'] : null;

                $storedRule = null;

                if ($ruleId !== null) {
                    $storedRule = $specificPriceRuleRepository->find($ruleId);
                }

                $form = $this->getFormFactory()->createNamed('', ProductSpecificPriceRuleType::class, $storedRule);

                $form->submit($dataRow);

                if ($form->isValid()) {
                    $formData = $form->getData();
                    $formData->setProduct($object->getId());

                    $prices[] = $formData;
                } else {
                    foreach ($form->getErrors(true, true) as $e) {
                        $errorMessageTemplate = $e->getMessageTemplate();
                        foreach ($e->getMessageParameters() as $key => $value) {
                            $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
                        }

                        $errors[] = sprintf('%s: %s', $e->getOrigin()->getConfig()->getName(), $errorMessageTemplate);
                    }

                    throw new \Exception(implode(\PHP_EOL, $errors));
                }
            }
        }

        return $prices;
    }

    /**
     * @param Concrete $object
     * @param array    $params
     */
    public function save($object, $params = [])
    {
        if ($object instanceof ProductInterface) {
            $existingPriceRules = $object->getObjectVar($this->getName());

            $entityMerger = new EntityMerger($this->getEntityManager());

            $all = $this->load($object, ['force' => true]);
            $founds = [];

            if (is_array($existingPriceRules)) {
                foreach ($existingPriceRules as $price) {
                    if ($price instanceof ProductSpecificPriceRuleInterface) {
                        $entityMerger->merge($price);

                        $price->setProduct($object->getId());

                        $this->getEntityManager()->persist($price);

                        $founds[] = $price->getId();
                    }
                }
            }

            foreach ($all as $price) {
                if (!in_array($price->getId(), $founds)) {
                    $this->getEntityManager()->remove($price);
                }
            }

            $this->getEntityManager()->flush();
        }
    }

    public function load($object, $params = [])
    {
        if (isset($params['force']) && $params['force']) {
            return $this->getProductSpecificPriceRuleRepository()->findForProduct($object);
        }

        return null;
    }

    public function delete($object, $params = [])
    {
        if ($object instanceof ProductInterface) {
            $all = $this->load($object, ['force' => true]);

            foreach ($all as $price) {
                $this->getEntityManager()->remove($price);
            }

            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param mixed $data
     * @param null  $relatedObject
     * @param mixed $params
     * @param null  $idMapper
     *
     * @return ProductSpecificPriceRuleInterface[]
     *
     * @throws \Exception
     */
    public function getFromWebserviceImport($data, $relatedObject = null, $params = [], $idMapper = null)
    {
        return $this->getDataFromEditmode($this->arrayCastRecursive($data), $relatedObject, $params);
    }

    /**
     * @param \stdClass[] $array
     *
     * @return array
     */
    protected function arrayCastRecursive($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $this->arrayCastRecursive($value);
                }
                if ($value instanceof \stdClass) {
                    $array[$key] = $this->arrayCastRecursive((array)$value);
                }
            }
        }
        if ($array instanceof \stdClass) {
            return $this->arrayCastRecursive((array)$array);
        }

        return $array;
    }

    public function getForCsvExport($object, $params = [])
    {
        return '';
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer()
    {
        return \Pimcore::getContainer();
    }

    /**
     * @return ProductSpecificPriceRuleRepositoryInterface
     */
    private function getProductSpecificPriceRuleRepository()
    {
        return $this->getContainer()->get('coreshop.repository.product_specific_price_rule');
    }

    /**
     * @return RepositoryFactoryInterface
     */
    private function getProductSpecificPriceRuleRepositoryFactory()
    {
        return $this->getContainer()->get('coreshop.repository.factory.product_specific_price_rule');
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    private function getFormFactory()
    {
        return $this->getContainer()->get('form.factory');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    private function getSerializer()
    {
        return $this->getContainer()->get('jms_serializer');
    }

    /**
     * @return array
     */
    private function getConfigActions()
    {
        return $this->getContainer()->getParameter('coreshop.product_specific_price_rule.actions');
    }

    /**
     * @return array
     */
    private function getConfigConditions()
    {
        return $this->getContainer()->getParameter('coreshop.product_specific_price_rule.conditions');
    }
}
