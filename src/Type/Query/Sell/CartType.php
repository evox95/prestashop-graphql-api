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

namespace PrestaShop\API\GraphQL\Type\Query\Sell;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Cart\CartProductType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Cart\CartRuleType;
use PrestaShop\API\GraphQL\Types;

class CartType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Cart',
            'fields' => [
                'products' => new ListOfType(Types::get(CartProductType::class)),
                'cart_rules' => new ListOfType(Types::get(CartRuleType::class)),
                'total' => Types::float(),
                'total_shipping_cost' => Types::float(),
                'total_weight' => Types::float(),
            ],
        ];
    }

    protected function getTotalWeight($objectValue, $args, ApiContext $context, ResolveInfo $info): float
    {
        return $context->shopContext->cart->getTotalWeight();
    }

    protected function getTotalShippingCost($objectValue, $args, ApiContext $context, ResolveInfo $info): float
    {
        return $context->shopContext->cart->getTotalShippingCost();
    }

    protected function getTotal($objectValue, $args, ApiContext $context, ResolveInfo $info): float
    {
        return $context->shopContext->cart->getCartTotalPrice();
    }

    protected function getProducts($objectValue, $args, ApiContext $context, ResolveInfo $info): array
    {
//        var_dump($_COOKIE);
//        var_dump($context->shopContext->cart->id);
//        die();
        return $context->shopContext->cart->getProducts();
    }

    protected function getCartRules($objectValue, $args, ApiContext $context, ResolveInfo $info): array
    {
        return $context->shopContext->cart->getCartRules();
    }
}
