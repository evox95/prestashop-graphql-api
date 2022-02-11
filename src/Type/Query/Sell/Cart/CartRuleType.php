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

namespace PrestaShop\API\GraphQL\Type\Query\Sell\Cart;

use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;

class CartRuleType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
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
        ];
    }
}
