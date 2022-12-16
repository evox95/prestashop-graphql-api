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

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;

class ProductCombinationType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'ProductCombination',
            'fields' => [
                'id' => Type::id(),
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
                'price' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'weight' => [
                    'type' => Type::float(),
                    'description' => '',
                ],
                'default_on' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
                'minimal_quantity' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'attributes' => [
                    'type' => new ListOfType(Types::get(ProductCombinationAttributeType::class)),
                    'description' => 'Attributes assigned to combination',
                ],
            ],
        ];
    }

}
