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

use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use Order;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Common\AddressType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Order\OrderProductType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Address;

class OrderType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Order',
            'fields' => [
                'products' => new ListOfType(Types::get(OrderProductType::class)),
                'date_add' => Types::string(),
                'reference' => Types::string(),
                'payment' => Types::string(),
                'current_state' => Types::id(),
                'id_address_delivery' => Types::id(),
                'address_delivery' => Types::get(AddressType::class),
                'id_address_invoice' => Types::id(),
                'address_invoice' => Types::get(AddressType::class),
                'id_currency' => Types::id(),
                'id_carrier' => Types::id(),
                'total_discounts' => Types::float(),
                'total_discounts_tax_incl' => Types::float(),
                'total_discounts_tax_excl' => Types::float(),
                'total_paid' => Types::float(),
                'total_paid_tax_incl' => Types::float(),
                'total_paid_tax_excl' => Types::float(),
                'total_paid_real' => Types::float(),
                'total_products' => Types::float(),
                'total_products_wt' => Types::float(),
                'total_shipping' => Types::float(),
                'total_shipping_tax_incl' => Types::float(),
                'total_shipping_tax_excl' => Types::float(),
                'total_wrapping' => Types::float(),
                'total_wrapping_tax_incl' => Types::float(),
                'total_wrapping_tax_excl' => Types::float(),
                'carrier_tax_rate' => Types::float(),
                'shipping_number' => Types::string(),
                'invoice_number' => Types::string(),
                'gift' => Types::boolean(),
                'gift_message' => Types::string(),
                'note' => Types::string(),
            ],
        ];
    }

    protected function getAddressDelivery(Order $objectValue, $args, ApiContext $context, ResolveInfo $info): Address
    {
        return new Address($objectValue->id_address_delivery);
    }

    protected function getAddressInvoice(Order $objectValue, $args, ApiContext $context, ResolveInfo $info): Address
    {
        return new Address($objectValue->id_address_invoice);
    }

    protected function getProducts(Order $objectValue, $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $products = $objectValue->getProducts();
        foreach ($products as $product) {
            foreach ($product as $key => $value) {
                if (stripos($key, 'product_') === 0) {
                    $product[substr($key, strlen('product_'))] = $value;
                    unset($product[$key]);
                }
            }
            yield $product;
        }
    }
}
