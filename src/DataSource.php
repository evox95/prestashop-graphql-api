<?php declare(strict_types=1);

namespace PrestaShop\Api;

use PrestaShop\Api\Data\Product;
use PrestaShop\Api\Data\Category;
use PrestaShop\Api\Exception\ApiException;
use PrestaShop\Api\Model\DataInterface;
use PrestaShop\PrestaShop\Adapter\Entity\ObjectModel;

class DataSource
{

    /** @var array<int, Product> */
    private static array $products = [];

    /**
     * @param string $name
     * @param array $arguments
     * @return DataInterface|string|null
     * @throws ApiException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (stripos($name, 'find') === 0) {
            return self::find(
                ucfirst(substr($name, strlen('find'))),
                ...$arguments
            );
        }

        throw new ApiException("Function $name not supported");
    }

    public static function find(string $name, int $id): ?DataInterface
    {
        $objectClassName = '\\PrestaShop\\Api\\Data\\' . $name;
        return new $objectClassName($id, true, 1);
    }

//    public static function findProduct(int $id): ?Product
//    {
//        return new Product($id, true, 1);
//    }

//    public static function findCategory(int $id): ?Category
//    {
//        return new Category($id, 1);
//    }
//
//    /**
//     * @return array<int, Product>
//     */
//    public static function findProducts(int $limit, ?int $afterId = null): array
//    {
//        // @todo
//        $start = null !== $afterId
//            ? (int)array_search($afterId, array_keys(self::$products), true) + 1
//            : 0;
//
//        return array_slice(array_values(self::$products), $start, $limit);
//    }

    /**
     * @return array<int, Category>
     */
    public static function findCategories(int $limit, ?int $afterId = null): array
    {
        // @todo
        return Category::getSimpleCategories(1);
    }
}