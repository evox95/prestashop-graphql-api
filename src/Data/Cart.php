<?php declare(strict_types=1);

namespace PrestaShop\Api\Data;

use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\Api\AppContext;
use PrestaShop\Api\Model\DataInterface;
use PrestaShop\PrestaShop\Adapter\Entity\Context;

class Cart extends \Cart implements DataInterface
{

    public static function get($objectValue, $args, AppContext $context, ResolveInfo $info): ?\Cart
    {
        $selectedFields = $info->getFieldSelection();

        if ($selectedFields['products'] ?? false) {
            $context->shopContext->cart->products = $context->shopContext->cart->getProducts();
        }

        if ($selectedFields['cart_rules'] ?? false) {
            $context->shopContext->cart->cart_rules = $context->shopContext->cart->getCartRules();
        }

        return $context->shopContext->cart;
    }

}