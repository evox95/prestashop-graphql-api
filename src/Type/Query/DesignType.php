<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query;

use CMS;
use Db;
use DbQuery;
use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\AppContext;
use PrestaShop\API\GraphQL\Type\Query\Design\PageType;
use PrestaShopDatabaseException;
use PrestaShopException;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;
use GraphQL\Type\Definition\NonNull;

class DesignType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
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
        ]);
    }

    /**
     * @param null $rootValue
     * @param array{id: string} $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return CMS|null
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getPage($rootValue, array $args, AppContext $context, ResolveInfo $info): ?CMS
    {
        return new CMS((int)$args['id'], $context->shopContext->language->id);
    }

    /**
     * @param null $rootValue
     * @param array{limit: int, after?: string} $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return Generator
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getPages($rootValue, array $args, AppContext $context, ResolveInfo $info): Generator
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('a.id_cms');
        $dbQuery->from(CMS::$definition['table'], 'a');

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