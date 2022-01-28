<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query\Sell\Cart;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CartRuleType extends ObjectType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'CartRule',
            'fields' => [
                'id_cart_rule' => [
                    'type' => Type::int(),
                    'description' => 'Id cart rule',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'Discount name',
                ],
                'description' => [
                    'type' => Type::string(),
                    'description' => 'Discount description',
                ],
                'code' => [
                    'type' => Type::string(),
                    'description' => 'Discount code',
                ],
                'gift_product' => [
                    'type' => Type::int(),
                    'description' => 'Gift - product id',
                ],
                'gift_product_attribute' => [
                    'type' => Type::string(),
                    'description' => 'Gift - product attribute id',
                ],
                'reduction_percent' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'reduction_amount' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'reduction_tax' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'reduction_product' => [
                    'type' => Type::int(),
                    'description' => 'Reduction for product id',
                ],
                'free_shipping' => [
                    'type' => Type::boolean(),
                    'description' => 'Is shipping free?',
                ],
                'highlight' => [
                    'type' => Type::boolean(),
                    'description' => 'Show in the cart',
                ],
            ],
        ]);
    }

}