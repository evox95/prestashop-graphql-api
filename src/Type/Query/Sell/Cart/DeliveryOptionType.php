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

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;

class DeliveryOptionType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'DeliveryOption',
            'fields' => [
                'id_address' => [
                    'type' => Type::id(),
                    'description' => '',
                ],
                'carrier_list' => [
                    'type' => new ListOfType(Types::get(DeliveryOptionCarrierType::class)),
                    'description' => '',
                ],
            ],
        ];
    }

    protected function getCarrierList(array $objectValue, $args, ApiContext $context, ResolveInfo $info)
    {
//        var_dump($objectValue['carriers']);
//        die();
        foreach ($objectValue['carriers'] as $data) {
            foreach ($data['carrier_list'] as $carrier) {
                yield $carrier;
            }
        }
    }

}
