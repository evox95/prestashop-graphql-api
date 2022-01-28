<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Mutation;

use PrestaShop\API\GraphQL\AppContext;
use Cart;
use PrestaShop\API\GraphQL\Type\Query\Sell\CartType as QueryCartType;
use PrestaShop\API\GraphQL\Types;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class CartMutation extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'CartMutation',
            'fields' => [
                'delete_product' => [
                    'type' => Types::get(QueryCartType::class),
                    'description' => 'Add product to cart',
                    'args' => [
                        'id_product' => new NonNull(Types::int()),
                        'id_product_attribute' => Types::int(),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): \Cart {
                        $this->cartUpdate($args);
//                        var_dump($errors);die();
                        return $context->shopContext->cart;
                    }
                ],
                'update_product' => [
                    'type' => Types::get(QueryCartType::class),
                    'description' => 'Add product to cart',
                    'args' => [
                        'id_product' => new NonNull(Types::int()),
                        'id_product_attribute' => Types::int(),
                        'quantity' => new NonNull(Types::int()),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): \Cart {
                        $args['qty'] = $args['quantity'];
                        $args['op'] = $args['quantity'] > 0 ? 'up' : 'down';
                        $args['id_product_attribute'] = $args['id_product_attribute'] ?? 0;
                        $args['update'] = true;
                        $this->cartUpdate($args);
                        return $context->shopContext->cart;
                    }
                ],
                'add_cart_rule' => [
                    'type' => Types::get(QueryCartType::class),
                    'description' => 'Add discount code',
                    'args' => [
                        'code' => new NonNull(Types::string()),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): ?\Cart {
                        $args['addDiscount'] = true;
                        $args['discount_name'] = $args['code'];
                        $this->cartUpdate($args);
                        return $context->shopContext->cart;
                    }
                ],
                'delete_cart_rule' => [
                    'type' => Types::get(QueryCartType::class),
                    'description' => 'Delete cart rule',
                    'args' => [
                        'id_cart_rule' => new NonNull(Types::int()),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): ?\Cart {
                        $args['deleteDiscount'] = $args['id_cart_rule'];
                        $this->cartUpdate($args);
                        return $context->shopContext->cart;
                    }
                ],
            ],
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($rootValue, $args, $context, $info);
            },
        ]);
    }

    private function cartUpdate(array $args)
    {
        $_GET = $args;
        $_GET['ajax'] = true;
        $cartCtrl = new \CartControllerCore();
        $cartCtrl->init();
        $cartCtrl->postProcess();
//        return $cartCtrl->errors;
    }
}