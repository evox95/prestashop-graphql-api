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

use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;

class SortOrderType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'SortOrder',
            'fields' => [
                'entity' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'field' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'direction' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'label' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'urlParameter' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'current' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
            ],
        ];
    }

}
