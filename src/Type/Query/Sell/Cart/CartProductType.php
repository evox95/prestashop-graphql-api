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

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;

class CartProductType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'CartProduct',
            'fields' => [
                'id_product' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'id_product_attribute' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'quantity' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'reference' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'ean13' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'isbn' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'upc' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'mpn' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'link_rewrite' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'description_short' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'is_virtual' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'available_now' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'available_later' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'id_category_default' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'id_supplier' => [
                    'type' => Type::id(),
                    'description' => 'Id of the supplier',
                ],
                'supplier_reference' => [
                    'type' => Type::string(),
                    'description' => 'Supplier reference',
                ],
                'id_manufacturer' => [
                    'type' => Type::id(),
                    'description' => 'Id of the manufacturer',
                ],
                'manufacturer_name' => [
                    'type' => Type::string(),
                    'description' => 'Name of the manufacturer',
                ],
                'on_sale' => [
                    'type' => Type::boolean(),
                    'description' => 'Is product on sale?',
                ],
                'ecotax' => [
                    'type' => Type::float(),
                    'description' => 'Ecotax value',
                ],
                'additional_shipping_cost' => [
                    'type' => Type::float(),
                    'description' => 'Additional shipping cost',
                ],
                'available_for_order' => [
                    'type' => Type::boolean(),
                    'description' => 'Is product available for order?',
                ],
                'width' => [
                    'type' => Type::float(),
                    'description' => 'Width',
                ],
                'height' => [
                    'type' => Type::float(),
                    'description' => 'Height',
                ],
                'depth' => [
                    'type' => Type::float(),
                    'description' => 'Depth',
                ],
                'minimal_quantity' => [
                    'type' => Type::int(),
                    'description' => 'Minimal buy quantity',
                ],
                'id_image' => [
                    'type' => Type::id(),
                    'description' => 'Id cover image',
                ],
                'price' => [
                    'type' => Type::float(),
                    'description' => 'Price without tax',
                ],
                'price_wt' => [
                    'type' => Type::float(),
                    'description' => 'Price with tax',
                ],
                'reduction' => [
                    'type' => Type::boolean(),
                    'description' => 'Is reduction?',
                ],
                'reduction_without_tax' => [
                    'type' => Type::float(),
                    'description' => 'Reduction without tax',
                ],
                'price_without_reduction' => [
                    'type' => Type::float(),
                    'description' => 'Price without reduction and with tax',
                ],
                'price_without_reduction_without_tax' => [
                    'type' => Type::float(),
                    'description' => 'Price without reduction and without tax',
                ],
                'price_with_reduction' => [
                    'type' => Type::float(),
                    'description' => 'Price with reduction and with tax',
                ],
                'price_with_reduction_without_tax' => [
                    'type' => Type::float(),
                    'description' => 'Price with reduction and without tax',
                ],
                'total' => [
                    'type' => Type::float(),
                    'description' => 'Total without tax',
                ],
                'total_wt' => [
                    'type' => Type::float(),
                    'description' => 'Total with tax',
                ],
                'attributes' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'attributes_small' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'rate' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'tax_name' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'cover_url' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
            ],
        ];
    }

    protected function getCoverUrl(array $rootValue, array $args, ApiContext $context, ResolveInfo $info): string
    {
        return $context->shopContext->link->getImageLink('none', $rootValue['id_image']);
    }
}
