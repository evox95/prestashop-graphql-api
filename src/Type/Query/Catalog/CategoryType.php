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

/*
 * This file is part of the evox95/prestashop-graphql-api package.
 *
 * (c) Mateusz Bartocha <contact@bestcoding.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaShop\API\GraphQL\Type\Query\Catalog;

use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;

class CategoryType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Category',
            'fields' => [
                'id' => Type::id(),
                'name' => Type::string(),
                'description' => Type::string(),
                'meta_title' => Type::string(),
                'meta_keywords' => Type::string(),
                'meta_description' => Type::string(),
                'id_parent' => Type::id(),
                'position' => Type::int(),
                'level_depth' => Type::int(),
            ],
        ];
    }
}
