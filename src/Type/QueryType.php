<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type;

use Customer;
use PrestaShop\API\GraphQL\AppContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\CatalogType;
use PrestaShop\API\GraphQL\Type\Query\CustomerType;
use PrestaShop\API\GraphQL\Type\Query\DesignType;
use PrestaShop\API\GraphQL\Type\Query\SellType;
use PrestaShop\API\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Query',
            'fields' => [
                'hello' => Type::string(),
                'customer' => [
                    'type' => Types::get(CustomerType::class),
                    'description' => 'Get customer',
                    'resolve' => fn(...$args) => $this->getCustomer(...$args),
                ],
                'catalog' => [
                    'type' => Types::get(CatalogType::class),
                    'description' => 'Catalog',
                ],
                'sell' => [
                    'type' => Types::get(SellType::class),
                    'description' => 'Sell',
                ],
                'design' => [
                    'type' => Types::get(DesignType::class),
                    'description' => 'Design',
                ],
            ],
        ]);
    }

    public function getCustomer($objectValue, $args, AppContext $context, ResolveInfo $info): Customer
    {
        return $context->shopContext->customer;
    }

    public function hello(): string
    {
        return 'Your PrestaShop Front API endpoint is ready! Use a GraphQL client to explore the schema.';
    }

}