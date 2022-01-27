<?php declare(strict_types=1);

namespace PrestaShop\Api\Type;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
//use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\Api\AppContext;
use PrestaShop\Api\Types;

class CartType extends ObjectType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'Cart',
            'fields' => [
//                'id' => Type::id(),
                'products' => new ListOfType(Types::get(CartProductType::class)),
                'cart_rules' => new ListOfType(Types::get(CartRuleType::class)),
                'total' => [
                    'type' => Types::float(),
                    'resolve' => function($objectValue, $args, AppContext $context, ResolveInfo $info): float {
                        return $context->shopContext->cart->getCartTotalPrice();
                    }
                ],
                'total_shipping_cost' => [
                    'type' => Types::float(),
                    'resolve' => function($objectValue, $args, AppContext $context, ResolveInfo $info): float {
                        return $context->shopContext->cart->getTotalShippingCost();
                    }
                ],
                'total_weight' => [
                    'type' => Types::float(),
                    'resolve' => function($objectValue, $args, AppContext $context, ResolveInfo $info): float {
                        return $context->shopContext->cart->getTotalWeight();
                    }
                ],
            ],
        ]);
    }

}