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

class FilterOptionType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'FilterOption',
            'fields' => [
                'label' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'type' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'displayed' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
                'active' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
                'properties' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                // todo: remove?
                'magnitude' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'value' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                // todo: remove?
                'nextEncodedFacets' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
            ],
        ];
    }

}
