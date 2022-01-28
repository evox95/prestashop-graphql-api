<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query;

use Cart;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\AppContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Sell\CartType;
use PrestaShop\API\GraphQL\Types;

class SellType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Sell',
            'fields' => [
//                'cart' => [
//                    'type' => Types::get(CartType::class),
//                    'description' => 'Returns cart',
//                ],
                'cart' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Get cart',
//                    'resolve' => fn(...$args) => \PrestaShop\API\GraphQL\Data\Cart::get(...$args)
                ],
            ],
        ]);
    }

    /**
     * @param null $rootValue
     * @param array{id: string} $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return Cart|null
     */
    public function getCart($rootValue, array $args, AppContext $context, ResolveInfo $info): ?Cart
    {
        return $context->shopContext->cart;
    }
}