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

namespace PrestaShop\API\GraphQL\Type\Query\Module\Ps_Linklist;

use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;

class LinkBlockType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'LinkBlock',
            'fields' => [
                'id' => Type::id(),
                'name' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'position' => [
                    'type' => Type::int(),
                    'description' => '',
                ],
                'content' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'custom_content' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
            ],
        ];
    }
}
