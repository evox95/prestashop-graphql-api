<?php
/*
 * This file is part of the evox95/prestashop-graphql-api package.
 *
 * (c) Mateusz Bartocha <contact@bestcoding.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Mutation\Sell;

use CartControllerCore;
use Context;
use Exception;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\Product;
use PrestaShop\PrestaShop\Adapter\Entity\Validate;

class CartMutation extends ObjectType
{
    /**
     * @throws Exception
     */
    protected static function getSchema(): array
    {
        return [
            'name' => 'CartMutation',
            'fields' => [
                'delete_product' => [
                    'type' => Types::boolean(),
                    'description' => 'Add product to cart',
                    'args' => [
                        'id_product' => new NonNull(Types::int()),
                        'id_product_attribute' => Types::int(),
                        'customization' => Types::string(),
                    ],
                ],
                'update_product' => [
                    'type' => Types::boolean(),
                    'description' => 'Add product to cart',
                    'args' => [
                        'id_product' => new NonNull(Types::int()),
                        'id_product_attribute' => Types::int(),
                        'customization' => Types::string(),
                        'quantity' => new NonNull(Types::int()),
                    ],
                ],
            ],
        ];
    }

    protected function actionUpdateProduct($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
    {
        if (!Validate::isLoadedObject($context->shopContext->cart)) {
            $context->shopContext->cart->add();
            $context->shopContext->cookie->id_cart = (int)$context->shopContext->cart->id;
        }

        $args['id_customization'] = $this->resolveProductCustomization(
            (int)$args['id_product'],
            (int)$args['id_product_attribute'],
            $args['customization'],
            $context->shopContext
        );

        $args['qty'] = $args['quantity'];
        $args['op'] = $args['quantity'] > 0 ? 'up' : 'down';
        $args['id_product_attribute'] = (int)$args['id_product_attribute'] ?? 0;
        $args['update'] = true;
        return $this->cartUpdate($args);
    }

    protected function actionDeleteProduct($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
    {
        $args['id_customization'] = $this->resolveProductCustomization(
            (int)$args['id_product'],
            (int)$args['id_product_attribute'],
            $args['customization'],
            $context->shopContext
        );

        $args['delete'] = true;
        return $this->cartUpdate($args);
    }

    private function cartUpdate(array $args): bool
    {
        $_GET = $args;
        $_GET['ajax'] = true;
        $this->cartCtrl = new CartControllerCore();
        $this->cartCtrl->init();
        $this->cartCtrl->postProcess();
        return true;
//        return $cartCtrl->errors;
    }

    private function addCustomization(Product $product, int $productAttributeId, Context $context, array $fields): bool
    {
        if (!$field_ids = $product->getCustomizationFieldIds()) {
            return false;
        }

        $authorized_text_fields = [];
        foreach ($field_ids as $field_id) {
            if ($field_id['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                $authorized_text_fields[(int)$field_id['id_customization_field']] = 'textField'
                    . (int)$field_id['id_customization_field'];
            }
        }

        $indexes = array_flip($authorized_text_fields);
        foreach ($fields as $field_name => $value) {
            if (
                in_array($field_name, $authorized_text_fields)
                && $value != ''
                && Validate::isMessage($value)
            ) {
                return $context->cart->_addCustomization(
                    $product->id,
                    $productAttributeId,
                    $indexes[$field_name],
                    Product::CUSTOMIZE_TEXTFIELD,
                    $value,
                    0
                );
            }
        }

        return true;
    }

    private function resolveProductCustomization(
        int     $productId,
        int     $productAttributeId,
        string  $customizationStr,
        Context $context,
        bool    $retrying = false
    ): int
    {
        parse_str($customizationStr, $fields);
        $idCustomization = 0;
        if (!$fields) {
            return $idCustomization;
        }

        $customizations = Db::getInstance()->executeS(
            'SELECT cu.id_customization, cd.index, cd.value
            FROM `' . _DB_PREFIX_ . 'customization` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON (cu.`id_customization` = cd.`id_customization`)
            WHERE cu.id_cart = ' . (int)$context->cart->id . '
                AND cu.id_product = ' . $productId . '
                AND cu.id_product_attribute = ' . $productAttributeId . '
                AND type = ' . Product::CUSTOMIZE_TEXTFIELD
        );
        $groups = [];
        foreach ($customizations as $customization) {
            if (!isset($groups[$customization['id_customization']])) {
                $groups[$customization['id_customization']] = [];
            }
            $groups[$customization['id_customization']][] = $customization;
        }
        foreach ($groups as $groupCustomizations) {
            foreach ($groupCustomizations as $c) {
                if (
                    !isset($fields['textField' . $c['index']])
                    || $fields['textField' . $c['index']] != $c['value']
                ) {
                    continue 2;
                }
            }
            $idCustomization = (int)$groupCustomizations[0]['id_customization'];
            break;
        }
        if (!$idCustomization && !$retrying) {
            $product = new Product($productId);
            $this->addCustomization($product, $productAttributeId, $context, $fields);
            return $this->resolveProductCustomization(
                $productId, $productAttributeId, $customizationStr, $context, true
            );
        }
        return $idCustomization;
    }
}
