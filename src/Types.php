<?php declare(strict_types=1);

namespace PrestaShop\Api;

use Exception;
use GraphQL\Type\Definition\Type;
use PrestaShop\Api\Type\ProductType;
use Closure;
use function count;
use function explode;
use GraphQL\Type\Definition\ScalarType;
use function lcfirst;
use function method_exists;
use function preg_replace;
use function strtolower;

final class Types
{

    /** @var array<string, Type> */
    private static array $types = [];

//    public static function product(): callable
//    {
//        return self::get(ProductType::class);
//    }

    /**
     * @param class-string<Type> $classname
     *
     * @return Closure(): Type
     */
    public static function get(string $classname): Closure
    {
        return static fn() => self::byClassName($classname);
    }

    /**
     * @param class-string<Type> $classname
     */
    private static function byClassName(string $classname): Type
    {
        $parts = explode('\\', $classname);

        $cacheName = strtolower(preg_replace('~Type$~', '', $parts[count($parts) - 1]));

        if (!isset(self::$types[$cacheName])) {
            return self::$types[$cacheName] = new $classname();
        }

        return self::$types[$cacheName];
    }

    public static function byTypeName(string $shortName): Type
    {
        $cacheName = strtolower($shortName);
        $type = null;

        if (isset(self::$types[$cacheName])) {
            return self::$types[$cacheName];
        }

        $method = lcfirst($shortName);
        if (method_exists(self::class, $method)) {
            $type = self::{$method}();
        }

        if (!$type) {
            throw new Exception('Unknown graphql type: ' . $shortName);
        }

        return $type;
    }

    public static function boolean(): ScalarType
    {
        return Type::boolean();
    }

    public static function float(): ScalarType
    {
        return Type::float();
    }

    public static function id(): ScalarType
    {
        return Type::id();
    }

    public static function int(): ScalarType
    {
        return Type::int();
    }

    public static function string(): ScalarType
    {
        return Type::string();
    }

}