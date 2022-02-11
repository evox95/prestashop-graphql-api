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

namespace PrestaShop\API\GraphQL;

use Closure;
use function count;
use Exception;
use function explode;
use GraphQL\Type\Definition\Type;
use function lcfirst;
use function method_exists;
use function preg_replace;
use function strtolower;

final class Types extends Type
{
    /**
     * @var array<string, Type>
     */
    private static array $types = [];

    /**
     * @param class-string<Type> $classname
     *
     * @return Closure(): Type
     */
    public static function get(string $classname): Closure
    {
        return static fn () => self::byClassName($classname);
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

    private static function loadClassOverride(string &$classname): void
    {
        $classname = str_replace(
            'PrestaShop\\API\\GraphQL\\',
            'PrestaShop\\API\\GraphQL\\',
            $classname
        );
        require_once __DIR__ . '/../override/modules/api_graphql/src/QueryType.php';
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
}
