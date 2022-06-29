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

use Exception;
use GraphQL\Type\Definition\ResolveInfo;
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
                        'delivery_option' => Types::string(),
                        'recyclable' => Types::boolean(),
                        'gift' => Types::boolean(),
                        'gift_message' => Types::string(),
                    ],
                ],
                'create_order' => [
                    'type' => Types::string(),
                    'description' => 'Create order',
                    'args' => [
                        'payment_option__action' => Types::string(),
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
        $orderCtrl = new \OrderControllerCore();
        $orderCtrl->init();
//        $orderCtrl->postProcess();
//        $orderCtrl->initContent();
        $session = $orderCtrl->getCheckoutSession();
        if (!$session->getCart()->id) {
            return false;
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
}