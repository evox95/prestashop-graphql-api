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

namespace PrestaShop\API\GraphQL\Type\Query\Design;

use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;

class PageType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Page',
            'fields' => [
                'id' => Type::id(),
                'indexation' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
                'position' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'id_cms_category' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'content' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'head_seo_title' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'meta_title' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'meta_description' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'meta_keywords' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'link_rewrite' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
            ],
        ];
    }
}
