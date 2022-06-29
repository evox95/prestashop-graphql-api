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

namespace PrestaShop\API\GraphQL\Type\Query\Sell\Checkout;

use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;

class DeliveryOptionType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'DeliveryOption',
            'fields' => [
                'delivery_option' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'carrier' => [
                    'type' => Types::get(DeliveryOptionCarrierType::class),
                    'description' => '',
                ],
            ],
        ];
    }

}
