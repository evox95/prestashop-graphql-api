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

namespace PrestaShop\API\GraphQL\Type\Query;

use Cart;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Sell\CartType;
use PrestaShop\API\GraphQL\Types;

class SellType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Sell',
            'fields' => [
                'cart' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Get cart',
                ],
            ],
        ];
    }

    /**
     * @param null $rootValue
     * @param array $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     *
     * @return Cart|null
     */
    public function getCart($rootValue, array $args, ApiContext $context, ResolveInfo $info): ?Cart
    {
        return $context->shopContext->cart;
    }
}
