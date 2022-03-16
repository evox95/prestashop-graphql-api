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

use Cart;
use CartControllerCore;
use Exception;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Sell\CartType;
use PrestaShop\API\GraphQL\Types;

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
                    'type' => Types::get(CartType::class),
                    'description' => 'Add product to cart',
                    'args' => [
                        'id_product' => new NonNull(Types::int()),
                        'id_product_attribute' => Types::int(),
                    ],
                ],
                'update_product' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Add product to cart',
                    'args' => [
                        'id_product' => new NonNull(Types::int()),
                        'id_product_attribute' => Types::int(),
                        'quantity' => new NonNull(Types::int()),
                    ],
                ],
                'add_cart_rule' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Add discount code',
                    'args' => [
                        'code' => new NonNull(Types::string()),
                    ],
                ],
                'delete_cart_rule' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Delete cart rule',
                    'args' => [
                        'id_cart_rule' => new NonNull(Types::int()),
                    ],
                ],
            ],
        ];
    }

    protected function actionAddCartRule($objectValue, $args, ApiContext $context, ResolveInfo $info): Cart
    {
        $args['addDiscount'] = true;
        $args['discount_name'] = $args['code'];
        $this->cartUpdate($args);

        return $context->shopContext->cart;
    }

    private function cartUpdate(array $args)
    {
        $_GET = $args;
        $_GET['ajax'] = true;
        $cartCtrl = new CartControllerCore();
        $cartCtrl->init();
        $cartCtrl->postProcess();
//        return $cartCtrl->errors;
    }

    protected function actionDeleteCartRule($objectValue, $args, ApiContext $context, ResolveInfo $info): Cart
    {
        $args['deleteDiscount'] = $args['id_cart_rule'];
        $this->cartUpdate($args);

        return $context->shopContext->cart;
    }

    protected function actionUpdateProduct($objectValue, $args, ApiContext $context, ResolveInfo $info): Cart
    {
        $args['qty'] = $args['quantity'];
        $args['op'] = $args['quantity'] > 0 ? 'up' : 'down';
        $args['id_product_attribute'] = $args['id_product_attribute'] ?? 0;
        $args['update'] = true;
        $this->cartUpdate($args);

        return $context->shopContext->cart;
    }

    protected function actionDeleteProduct($objectValue, $args, ApiContext $context, ResolveInfo $info): Cart
    {
        $args['delete'] = true;
        $this->cartUpdate($args);

        return $context->shopContext->cart;
    }
}
