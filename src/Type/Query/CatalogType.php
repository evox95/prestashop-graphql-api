<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query;

use Db;
use DbQuery;
use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\AppContext;
use Category;
use PrestaShopDatabaseException;
use Product;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Catalog\CategoryType;
use PrestaShop\API\GraphQL\Type\Query\Catalog\ProductType;
use PrestaShop\API\GraphQL\Types;
use GraphQL\Type\Definition\NonNull;

class CatalogType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Catalog',
            'fields' => [
                'product' => [
                    'type' => Types::get(ProductType::class),
                    'description' => 'Returns product by id',
                    'args' => [
                        'id' => new NonNull(Types::id()),
                    ],
                ],
                'category' => [
                    'type' => Types::get(CategoryType::class),
                    'description' => 'Returns category by id',
                    'args' => [
                        'id' => new NonNull(Types::id()),
                    ],
                ],
                'products' => [
                    'type' => new ListOfType(Types::get(ProductType::class)),
                    'description' => 'Returns subset of products',
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
                        'id_category' => [
                            'type' => Types::id(),
                            'description' => 'Filter by category id',
                        ],
                        'id_category_default' => [
                            'type' => Types::id(),
                            'description' => 'Filter by category default id',
                        ],
                    ],
                ],
                'categories' => [
                    'type' => new ListOfType(Types::get(CategoryType::class)),
                    'description' => 'Returns subset of categories',
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
                        'id_parent' => [
                            'type' => Types::id(),
                            'description' => 'Filter by parent id',
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
     * @return Product|null
     */
    public function getProduct($rootValue, array $args, AppContext $context, ResolveInfo $info): ?Product
    {
        return new Product((int)$args['id'], true, $context->shopContext->language->id);
    }

    /**
     * @param null $rootValue
     * @param array{id: string} $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return Category|null
     */
    public function getCategory($rootValue, array $args, AppContext $context, ResolveInfo $info): ?Category
    {
        return new Category((int)$args['id'], $context->shopContext->language->id);
    }

    /**
     * @param null $rootValue
     * @param array{limit: int, after?: string} $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return Generator
     * @throws PrestaShopDatabaseException
     */
    public function getProducts($rootValue, array $args, AppContext $context, ResolveInfo $info): Generator
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('a.id_product');
        $dbQuery->from(Product::$definition['table'], 'a');

        $filter = $args['id_category_default'] ?? 0;
        if ($filter) {
            $dbQuery->where('a.id_category_default = ' . (int)$filter);
        }
        $filter = $args['id_category'] ?? 0;
        if ($filter) {
            $dbQuery->innerJoin('category_product', 'cp', 'a.id_product = cp.id_product');
            $dbQuery->where('cp.id_category = ' . (int)$filter);
        }
        $dbQuery->limit($args['limit'], $args['offset']);

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbQuery);
        foreach ($results as $result) {
            yield new Product($result[Product::$definition['primary']], true, $context->shopContext->language->id);
        }
    }

    /**
     * @param null $rootValue
     * @param array{limit: int, after?: string} $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return Generator
     * @throws PrestaShopDatabaseException
     */
    public function getCategories($rootValue, array $args, AppContext $context, ResolveInfo $info): Generator
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('a.id_category');
        $dbQuery->from(Category::$definition['table'], 'a');

        $filter = $args['id_parent'] ?? 0;
        if ($filter) {
            $dbQuery->where('a.id_parent = ' . (int)$filter);
        }
        $dbQuery->limit($args['limit'], $args['offset']);

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbQuery);
        foreach ($results as $result) {
            yield new Category($result[Category::$definition['primary']], $context->shopContext->language->id);
        }
    }
}