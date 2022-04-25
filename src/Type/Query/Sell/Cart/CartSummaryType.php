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

class CartSummaryType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'CartSummary',
            'fields' => [
                'is_virtual_cart' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'total_discounts' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_discounts_tax_exc' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_wrapping' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_wrapping_tax_exc' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_shipping' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_shipping_tax_exc' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_products' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_products_tax_exc' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_price' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_tax' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'total_price_tax_exc' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'is_multi_address_delivery' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
            ],
        ];
    }
}
