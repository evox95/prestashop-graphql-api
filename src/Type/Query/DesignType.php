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

use Exception;
use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Design\PageType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\CMS;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\DbQuery;
use PrestaShopDatabaseException;
use PrestaShopException;

class DesignType extends ObjectType
{
    /**
     * @throws Exception
     */
    protected static function getSchema(): array
    {
        return [
            'name' => 'Design',
            'fields' => [
                'page' => [
                    'type' => Types::get(PageType::class),
                    'description' => 'Returns page by id',
                    'args' => [
                        'id' => new NonNull(Types::id()),
                    ],
                ],
                'pages' => [
                    'type' => new ListOfType(Types::get(PageType::class)),
                    'description' => 'Returns subset of pages',
                    'args' => [
                        'offset' => [
                            'type' => Types::int(),
                            'description' => 'Offset',
                            'defaultValue' => 0,
                        ],
                        'limit' => [
                            'type' => Types::int(),
                            'description' => 'Limit',
                            'defaultValue' => 10,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param null $rootValue
     * @param array{id: int} $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     *
     * @return CMS
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getPage($rootValue, array $args, ApiContext $context, ResolveInfo $info): CMS
    {
        $object = new CMS((int) $args['id'], $context->shopContext->language->id);

        return $object->active ? $object : new CMS();
    }

    /**
     * @param null $rootValue
     * @param array{limit: int, offset: int} $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     *
     * @return Generator<CMS>
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getPages($rootValue, array $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('a.id_cms');
        $dbQuery->from(CMS::$definition['table'], 'a');
        $dbQuery->where('a.active = 1');

//        $filter = $args['id_category_default'] ?? 0;
//        if ($filter) {
//            $dbQuery->where('a.id_category_default = ' . (int)$filter);
//        }
        $dbQuery->limit($args['limit'], $args['offset']);

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbQuery);
        foreach ($results as $result) {
            yield new CMS($result[CMS::$definition['primary']], true, $context->shopContext->language->id);
        }
    }
}
