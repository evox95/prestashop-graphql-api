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

namespace PrestaShop\API\GraphQL\Type\Mutation\Sell;

use CartControllerCore;
use Exception;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use OrderControllerCore;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Order;
use PrestaShop\PrestaShop\Adapter\Entity\Validate;

class CheckoutMutation extends ObjectType
{
    /**
     * @throws Exception
     */
    protected static function getSchema(): array
    {
        return [
            'name' => 'CheckoutMutation',
            'fields' => [
                'update_delivery' => [
                    'type' => Types::boolean(),
                    'description' => 'Set delivery options',
                    'args' => [
                        'id_address_delivery' => Types::id(),
                        'id_address_invoice' => Types::id(),
                        'delivery_option' => Types::string(),
                        'recyclable' => Types::boolean(),
                        'gift' => Types::boolean(),
                        'gift_message' => Types::string(),
                        'message' => Types::string(),
                    ],
                ],
                'create_order' => [
                    'type' => Types::string(),
                    'description' => 'Create order',
                    'args' => [
                        'payment_option__action' => Types::string(),
                    ],
                ],
                'add_cart_rule' => [
                    'type' => Types::boolean(),
                    'description' => 'Add discount code',
                    'args' => [
                        'code' => new NonNull(Types::string()),
                    ],
                ],
                'delete_cart_rule' => [
                    'type' => Types::boolean(),
                    'description' => 'Delete cart rule',
                    'args' => [
                        'id_cart_rule' => new NonNull(Types::id()),
                    ],
                ],
//                'update_payment' => [
//                    'type' => Types::boolean(),
//                    'description' => 'Set delivery options',
//                    'args' => [
//                        'payment_option' => Types::string(),
//                    ],
//                ],
            ],
        ];
    }

//    protected function actionUpdatePayment($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
//    {
//        $orderCtrl = new \OrderControllerCore();
//        $orderCtrl->init();
////        $orderCtrl->postProcess();
////        $orderCtrl->initContent();
//        $session = $orderCtrl->getCheckoutSession();
//        if (!$session->getCart()->id) {
//            return false;
//        }
//
//        $result = true;
//        if (isset($args['payment_option'])) {
//            $result &= $session->s
//        }
//
//        return (bool)$result;
//    }

    protected function actionCreateOrder($objectValue, $args, ApiContext $context, ResolveInfo $info): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $args['payment_option__action']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE']);
        curl_exec($ch);
        curl_close($ch);
        if (!$context->shopContext->cart->orderExists()) {
            return null;
        }

        $order = Order::getByCartId($context->shopContext->cart->id);
        if (!Validate::isLoadedObject($order)) {
            return null;
        }
        return $order->reference;
    }

    protected function actionUpdateDelivery($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
    {
        $orderCtrl = new OrderControllerCore();
        $orderCtrl->init();
//        $orderCtrl->postProcess();
//        $orderCtrl->initContent();
        $session = $orderCtrl->getCheckoutSession();
        $cart = $session->getCart();
        if (!$cart->id) {
            return false;
        }

        if (isset($args['message'])) {
            $session->setMessage(pSQL(trim($args['message'])));
        }

        if (isset($args['id_address_invoice'])) {
            $session->setIdAddressInvoice((int)$args['id_address_invoice']);
//            $targetDeliveryAddressId = $cart->id_address_delivery;
//            $cart->updateAddressId($cart->id_address_invoice, (int)$args['id_address_invoice']);
//            $cart->updateDeliveryAddressId($cart->id_address_delivery, $targetDeliveryAddressId);
        }

        if (isset($args['id_address_delivery'])) {
            $cart->updateAddressId($cart->id_address_delivery, (int)$args['id_address_delivery']);
        }

        $result = true;
        if (isset($args['delivery_option'])) {
            $result &= $session->setDeliveryOption(
                [$context->shopContext->cart->id_address_delivery => $args['delivery_option']]
            );
        }
        if (isset($args['recyclable'])) {
            $result &= $session->setRecyclable($args['recyclable']);
        }
        if (isset($args['gift'])) {
            $result &= $session->setGift(
                $args['gift'],
                ($args['gift'] && isset($args['gift_message'])) ? $args['gift_message'] : ''
            );
        }

        return (bool)$result;
    }

    protected function actionAddCartRule($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
    {
        $args['addDiscount'] = true;
        $args['discount_name'] = $args['code'];
        return $this->cartUpdate($args);
    }

    protected function actionDeleteCartRule($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
    {
        $args['deleteDiscount'] = $args['id_cart_rule'];
        return $this->cartUpdate($args);
    }

    private function cartUpdate(array $args): bool
    {
        $_GET = $args;
        $_GET['ajax'] = true;
        $cartCtrl = new CartControllerCore();
        $cartCtrl->init();
        $cartCtrl->postProcess();
        return true;
//        return $cartCtrl->errors;
    }
}
