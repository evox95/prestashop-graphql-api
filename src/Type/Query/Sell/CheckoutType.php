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
use OrderControllerCore;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Common\AddressType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Checkout\CartRuleType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Checkout\DeliveryOptionType;
use PrestaShop\API\GraphQL\Type\Query\Sell\Checkout\PaymentOptionType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\PaymentOptionsFinder;
use PrestaShop\PrestaShop\Adapter\Entity\Shop;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class CheckoutType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Checkout',
            'fields' => [
                'delivery_option' => Types::get(DeliveryOptionType::class),
                'delivery_options' => new ListOfType(Types::get(DeliveryOptionType::class)),

                'payment_options' => new ListOfType(Types::get(PaymentOptionType::class)),
                'payment_option_last_selected' => Types::get(PaymentOptionType::class),

                'id_address_delivery' => Types::id(),
                'address_delivery' => Types::get(AddressType::class),
                'id_address_invoice' => Types::id(),
                'address_invoice' => Types::get(AddressType::class),

                'message' => Types::string(),

                'cart_rules' => new ListOfType(Types::get(CartRuleType::class)),
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

    protected function getPaymentOptionLastSelected(
        Cart        $objectValue,
        array       $args,
        ApiContext  $context,
        ResolveInfo $info
    ): ?PaymentOption
    {
        $lastOrderPaymentModuleName = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT o.module
            FROM `' . _DB_PREFIX_ . 'orders` o
            WHERE o.`id_customer` = ' . (int)$context->shopContext->customer->id .
            Shop::addSqlRestriction(Shop::SHARE_ORDER) . '
            ORDER BY o.`date_add` DESC'
        );
        if ($lastOrderPaymentModuleName) {
            foreach ($this->getPaymentOptionsList() as $paymentOption) {
                if ($paymentOption->getModuleName() == $lastOrderPaymentModuleName) {
                    return $paymentOption;
                }
            }
        }
        return null;
    }

    protected function getPaymentOptions(Cart $objectValue, $args, ApiContext $context, ResolveInfo $info): Generator
    {
        return $this->getPaymentOptionsList();
    }

    protected function getMessage(Cart $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        $orderCtrl = new OrderControllerCore();
        $orderCtrl->init();
//        $orderCtrl->postProcess();
//        $orderCtrl->initContent();
        $session = $orderCtrl->getCheckoutSession();
        if (!$session->getCart()->id) {
            return '';
        }
        return (string)$session->getMessage();
    }

    protected function getDeliveryOptions(Cart $objectValue, $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $list = $context->shopContext->cart->getDeliveryOptionList();
        foreach ($list[$objectValue->id_address_delivery] as $deliveryOptionStr => $data) {
            yield [
                'delivery_option' => $deliveryOptionStr,
                'carrier' => $data['carrier_list'][(int)$deliveryOptionStr],
            ];
        }
    }

    protected function getDeliveryOption(Cart $objectValue, $args, ApiContext $context, ResolveInfo $info): array
    {
        $option = $objectValue->getDeliveryOption();
        if (!$option) {
            $lastOrderCarrierId = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                'SELECT o.`id_carrier`
                FROM `' . _DB_PREFIX_ . 'orders` o
                WHERE o.`id_customer` = ' . (int)$context->shopContext->customer->id .
                Shop::addSqlRestriction(Shop::SHARE_ORDER) . '
                ORDER BY o.`date_add` DESC'
            );
            if (!$lastOrderCarrierId) {
                return [];
            }
            $option = $lastOrderCarrierId . ',';
        }

        $deliveryOptionStr = $option[$objectValue->id_address_delivery];
        $list = $objectValue->getDeliveryOptionList();
        $carrier = $list[$objectValue->id_address_delivery][$deliveryOptionStr]['carrier_list'][(int)$deliveryOptionStr];

        return [
            'delivery_option' => $option[$objectValue->id_address_delivery],
            'carrier' => $carrier,
        ];
    }

    private function getPaymentOptionsList(): Generator
    {
        $paymentOptionsFinder = new PaymentOptionsFinder();
        $list = $paymentOptionsFinder->find();
        foreach ($list as $paymentOptions) {
            /** @var PaymentOption[] $paymentOptions */
            foreach ($paymentOptions as $paymentOption) {
                yield $paymentOption;
            }
        }
    }

    protected function getCartRules($objectValue, $args, ApiContext $context, ResolveInfo $info): array
    {
        return $context->shopContext->cart->getCartRules();
    }
}
