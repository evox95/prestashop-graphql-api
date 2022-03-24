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

class PaginationType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Pagination',
            'fields' => [
                'total_items' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'items_shown_from' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'items_shown_to' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'current_page' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'pages_count' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'should_be_displayed' => [
                    'type' => Type::boolean(),
                ],
            ],
        ];
    }
}
