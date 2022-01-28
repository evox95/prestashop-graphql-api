<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query\Sell;

use GraphQL\Type\Definition\ListOfType;

use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\AppContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Cart\CartProductType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Cart\CartRuleType;
use PrestaShop\API\GraphQL\Types;

class CartType extends ObjectType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'Cart',
            'fields' => [
                'products' => new ListOfType(Types::get(CartProductType::class)),
                'cart_rules' => new ListOfType(Types::get(CartRuleType::class)),
                'total' => Types::float(),
                'total_shipping_cost' => Types::float(),
                'total_weight' => Types::float(),
            ],
        ]);
    }

    protected function getTotalWeight($objectValue, $args, AppContext $context, ResolveInfo $info): float
    {
        return $context->shopContext->cart->getTotalWeight();
    }

    protected function getTotalShippingCost($objectValue, $args, AppContext $context, ResolveInfo $info): float
    {
        return $context->shopContext->cart->getTotalShippingCost();
    }

    protected function getTotal($objectValue, $args, AppContext $context, ResolveInfo $info): float
    {
        return $context->shopContext->cart->getCartTotalPrice();
    }

    protected function getProducts($objectValue, $args, AppContext $context, ResolveInfo $info): array
    {
        return $context->shopContext->cart->getProducts();
    }

    protected function getCartRules($objectValue, $args, AppContext $context, ResolveInfo $info): array
    {
        return $context->shopContext->cart->getCartRules();
    }

}