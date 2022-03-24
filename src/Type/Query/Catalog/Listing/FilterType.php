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

namespace PrestaShop\API\GraphQL\Type\Query\Catalog\Listing;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;

class FilterType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Filter',
            'fields' => [
                'label' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'displayed' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
                'type' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'properties' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'multipleSelectionAllowed' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
                'widgetType' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'options' => [
                    'type' => new ListOfType(Types::get(FilterOptionType::class)),
                    'description' => '',
                ],
            ],
        ];
    }


}
