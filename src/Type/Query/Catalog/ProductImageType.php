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

namespace PrestaShop\API\GraphQL\Type\Query\Catalog;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\PrestaShop\Adapter\Entity\Db;

class ProductImageType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'ProductImage',
            'fields' => [
                'id' => Type::id(),
                'legend' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'url' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'position' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'product_attribute_id' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
            ],
        ];
    }

    protected function getUrl($rootValue, array $args, ApiContext $context, ResolveInfo $info): string
    {
        return $context->shopContext->link->getImageLink('large_default', $rootValue['id']);
    }

    protected function getProductAttributeId($rootValue, array $args, ApiContext $context, ResolveInfo $info): int
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_product_attribute`
            FROM `' . _DB_PREFIX_ . 'product_attribute_image`
            WHERE `id_image` = ' . (int)$rootValue['id']
        );
    }

}
