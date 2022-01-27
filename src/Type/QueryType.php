<?php declare(strict_types=1);

namespace PrestaShop\Api\Type;

use GraphQL\Type\Definition\ListOfType;
use PrestaShop\Api\AppContext;
use PrestaShop\Api\Data\Cart;
use PrestaShop\Api\Data\Category;
use PrestaShop\Api\Data\Product;
use PrestaShop\Api\Types;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\Api\DataSource;

class QueryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Query',
            'fields' => [
                'customer' => [
                    'type' => Types::get(CustomerType::class),
                    'description' => 'Get customer',
                    'resolve' => function($objectValue, $args, AppContext $context, ResolveInfo $info): ?\Customer {
                        return $context->shopContext->customer;
                    }
                ],
                'cart' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Get cart',
                    'resolve' => fn(...$args) => Cart::get(...$args)
                ],
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
                        'id_category' => new NonNull(Types::id()),
                    ],
                ],
                'categories' => [
                    'type' => new ListOfType(Types::get(CategoryType::class)),
                    'description' => 'Returns subset of categories',
                    'args' => [
                        'after' => [
                            'type' => Types::id(),
                            'description' => 'Fetch categories listed after the story with this ID',
                        ],
                        'limit' => [
                            'type' => Types::int(),
                            'description' => 'Number of categories to be returned',
                            'defaultValue' => 10,
                        ],
                    ],
                ],
                'hello' => Type::string(),
            ],
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
//                return DataSource::{'find' . ucfirst($name)}(...$arguments);
                return $this->{$info->fieldName}($rootValue, $args, $context, $info);
            },
        ]);
    }

//    public function __call(string $name, array $arguments)
//    {
////        var_dump($arguments);
//        return DataSource::{'find' . ucfirst($name)}(...$arguments);
//    }

    /**
     * @param null $rootValue
     * @param array{id: string} $args
     * @return Product|null
     */
    public function product($rootValue, array $args): ?Product
    {
        return DataSource::findProduct((int)$args['id']);
    }

    /**
     * @param null $rootValue
     * @param array{limit: int, after?: string} $args
     * @return array<int, Product>
     */
    public function products($rootValue, array $args): array
    {
        return DataSource::findProducts(
            $args['limit'],
            isset($args['after'])
                ? (int)$args['after']
                : null
        );
    }

    /**
     * @param null $rootValue
     * @param array{id: string} $args
     * @return Category|null
     */
    public function category($rootValue, array $args): ?Category
    {
        return DataSource::findCategory((int)$args['id']);
    }

    /**
     * @param null $rootValue
     * @param array{limit: int, after?: string} $args
     * @return array<int, Category>
     */
    public function categories($rootValue, array $args): array
    {
        return DataSource::findCategories(
            $args['limit'],
            isset($args['after'])
                ? (int)$args['after']
                : null
        );
    }

    public function hello(): string
    {
        return 'Your graphql-php endpoint is ready! Use a GraphQL client to explore the schema.';
    }

    public function deprecatedField(): string
    {
        return 'You can request deprecated field, but it is not displayed in auto-generated documentation by default.';
    }
}