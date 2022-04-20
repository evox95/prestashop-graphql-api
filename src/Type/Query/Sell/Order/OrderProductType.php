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

namespace PrestaShop\API\GraphQL\Type\Query\Sell\Order;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\PrestaShop\Adapter\Entity\Image;

class OrderProductType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'OrderProduct',
            'fields' => [
                'id' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'attribute_id' => [
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
                'supplier_reference' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'price' => [
                    'type' => Type::float(),
                    'description' => 'Price without tax',
                ],
                'tax_name' => ['type' => Type::string()],
                'tax_rate' => ['type' => Type::float()],
                'unit_price_tax_incl' => ['type' => Type::float()],
                'unit_price_tax_excl' => ['type' => Type::float()],
                'total_price_tax_incl' => ['type' => Type::float()],
                'total_price_tax_excl' => ['type' => Type::float()],
                'total_shipping_price_tax_excl' => ['type' => Type::float()],
                'total_shipping_price_tax_incl' => ['type' => Type::float()],
            ],
        ];
    }

    protected function getCoverUrl(array $rootValue, array $args, ApiContext $context, ResolveInfo $info): string
    {
        $images = Image::getImages($context->shopContext->language->id, $rootValue['id']);
        return $context->shopContext->link->getImageLink('none', $images[0]['id_image']);
    }
}
