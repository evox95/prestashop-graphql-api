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

namespace PrestaShop\API\GraphQL\Type\Query\Sell\Cart;

use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;

class CustomizationType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Customization',
            'fields' => [
                'id' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'name' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'value' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'index' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
            ],
        ];
    }

}
