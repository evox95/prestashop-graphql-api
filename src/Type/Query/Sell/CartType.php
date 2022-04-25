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

use Cart;
use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Common\AddressType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Cart\CartProductType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Cart\CartRuleType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Cart\CartSummaryType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Cart\DeliveryOptionType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Address;

class CartType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Cart',
            'fields' => [
                'products' => new ListOfType(Types::get(CartProductType::class)),
                'summary' => Types::get(CartSummaryType::class),

                'cart_rules' => new ListOfType(Types::get(CartRuleType::class)),
                'id_carrier' => Types::id(),
                'id_address_delivery' => Types::id(),
                'address_delivery' => Types::get(AddressType::class),
                'id_address_invoice' => Types::id(),
                'address_invoice' => Types::get(AddressType::class),
                'delivery_options' => new ListOfType(Types::get(DeliveryOptionType::class)),
            ],
        ];
    }

    protected function getAddressDelivery(Cart $objectValue, $args, ApiContext $context, ResolveInfo $info): Address
    {
        return new Address($objectValue->id_address_delivery);
    }

    protected function getAddressInvoice(Cart $objectValue, $args, ApiContext $context, ResolveInfo $info): Address
    {
        return new Address($objectValue->id_address_invoice);
    }

    protected function getDeliveryOptions(Cart $objectValue, $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $list = $context->shopContext->cart->getDeliveryOptionList();
        foreach ($list as $id_address => $data) {
            yield [
                'id_address' => $id_address,
                'carriers' => $data,
            ];
        }
    }

    protected function getSummary($objectValue, $args, ApiContext $context, ResolveInfo $info): array
    {
        $summary = $context->shopContext->cart->getSummaryDetails();

        $summary['total_products_tax_exc'] = $summary['total_products'];
        $summary['total_products'] = $summary['total_products_wt'];
        unset($summary['total_products_wt']);

        $summary['total_products_tax_exc'] = $summary['total_price_without_tax'];
        unset($summary['total_price_without_tax']);

        return $summary;
    }

    protected function getProducts($objectValue, $args, ApiContext $context, ResolveInfo $info): array
    {
        return $context->shopContext->cart->getProducts();
    }

    protected function getCartRules($objectValue, $args, ApiContext $context, ResolveInfo $info): array
    {
        return $context->shopContext->cart->getCartRules();
    }
}
