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
use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use ObjectModel;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Sell\CartType;
use PrestaShop\API\GraphQL\Type\Query\Sell\OrderType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\Order;
use PrestaShop\PrestaShop\Adapter\Entity\Shop;
use PrestaShop\PrestaShop\Adapter\Entity\Validate;
use PrestaShopDatabaseException;
use PrestaShopException;

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
                'order' => [
                    'type' => Types::get(OrderType::class),
                    'description' => 'Get order',
                    'args' => [
                        'reference' => new NonNull(Types::string()),
                    ],
                ],
                'orders' => [
                    'type' => new ListOfType(Types::get(OrderType::class)),
                    'description' => 'Get orders',
                    'args' => [
                        'date_from' => Types::string(),
                        'date_to' => Types::string(),
                    ],
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

    /**
     * @param null $rootValue
     * @param array $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     * @return ObjectModel|null
     */
    public function getOrder($rootValue, array $args, ApiContext $context, ResolveInfo $info): ?ObjectModel
    {
        $reference = $args['reference'];
        if (!Validate::isReference($reference)) {
            return null;
        }

        /** @var bool|\Order $order */
        $order = Order::getByReference($reference)->getFirst();
        if (!$order) {
            return null;
        }

        if ($order->id_customer != $context->shopContext->customer->id) {
            return null;
        }

        return $order;
    }

    /**
     * @param null $rootValue
     * @param array $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     * @return Generator
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getOrders($rootValue, array $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $dateFrom = $args['date_from'] ?? null;
        $dateTo = $args['date_to'] ?? null;
        if ($dateFrom && !Validate::isDate($dateFrom)) {
            return null;
        }
        if ($dateFrom && !Validate::isDate($dateTo)) {
            $dateTo = date('Y-m-d');
        }

        $query = 'SELECT o.`id_order`
            FROM `' . _DB_PREFIX_ . 'orders` o
            WHERE 
                ' . (
            $dateFrom && $dateTo
                ? 'o.`date_add` BETWEEN \'' . $dateFrom . ' 00:00:00\' AND \'' . $dateTo . ' 23:59:59\' AND'
                : ''
            ) . '
                o.`id_customer` = ' . (int)$context->shopContext->customer->id
            . Shop::addSqlRestriction(Shop::SHARE_ORDER) . '
            ORDER BY o.`date_add` DESC';
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        foreach ($res as $val) {
            yield new Order($val['id_order']);
        }
    }
}
