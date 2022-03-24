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

namespace PrestaShop\API\GraphQL\Type\Query\Catalog\Listing;

use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Product;

class ResultsType extends ObjectType
{

    protected static function getSchema(): array
    {
        return [
            'name' => 'Results',
            'fields' => [
                'products' => [
                    'type' => new ListOfType(Types::get(ProductType::class)),
                    'description' => '',
                ],
                'pagination' => [
                    'type' => Types::get(PaginationType::class),
                    'description' => '',
                ],
                'filters' => [
                    'type' => new ListOfType(Types::get(FilterType::class)),
                    'description' => '',
                ],
                'filters_active' => [
                    'type' => new ListOfType(Types::get(FilterOptionType::class)),
                    'description' => '',
                ],
                'sort_selected' => [
                    'type' => Types::string(),
                    'description' => '',
                ],
                'sort_orders' => [
                    'type' => new ListOfType(Types::get(SortOrderType::class)),
                    'description' => '',
                ],
            ],
        ];
    }

    public function getProducts($rootValue, array $args, ApiContext $context, ResolveInfo $info): Generator
    {
        foreach ($context->productSearchResults['products'] as $product) {
            yield new Product($product[Product::$definition['primary']], true, $context->shopContext->language->id);
        }
    }

    public function getPagination($rootValue, array $args, ApiContext $context, ResolveInfo $info): array
    {
        return $context->productSearchResults['pagination'];
    }

    public function getFiltersActive($rootValue, array $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $filters = $context->productSearchResults['facets']['activeFilters'] ?? [];
        foreach ($filters as $filter) {
            $filter['properties'] = json_encode($filter['properties']);
            yield $filter;
        }
    }

    public function getSortOrders($rootValue, array $args, ApiContext $context, ResolveInfo $info): array
    {
        return $context->productSearchResults['sort_orders'];
    }

    public function getSortSelected($rootValue, array $args, ApiContext $context, ResolveInfo $info): string
    {
        return (string)$context->productSearchResults['sort_selected'];
    }

    public function getFilters($rootValue, array $args, ApiContext $context, ResolveInfo $info): Generator
    {
        /** @var \PrestaShop\PrestaShop\Core\Product\Search\Facet[] $facets */
        $filters = $context->productSearchResults['facets']['filters'] ?? false;
        if (!$filters) return;
        foreach ($filters->getFacets() as $filter) {
            $data = $filter->toArray();

            $data['options'] = $data['filters'];
            unset($data['filters']);

            foreach ($data['options'] as &$option) {
                $option['properties'] = json_encode($option['properties']);
            }

            yield $data;
        }
    }
}
