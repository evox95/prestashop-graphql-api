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

namespace PrestaShop\API\GraphQL\Model;

use GraphQL\Type\Definition\ResolveInfo;
use HaydenPierce\ClassFinder\ClassFinder;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Types;

abstract class ObjectType extends \GraphQL\Type\Definition\ObjectType
{
    abstract protected static function getSchema(): array;

    public function __construct()
    {
        parent::__construct(array_merge(
            [
                'resolveField' => fn (...$args) => $this->resolveField(...$args),
            ],
            static::getSchema()
        ));
    }

    protected function deprecatedField(): string
    {
        return 'You can request deprecated field, but it is not displayed in auto-generated documentation by default.';
    }

    protected function resolveField($rootValue, array $args, ApiContext $context, ResolveInfo $info)
    {
        $getterMethodName = $this->camelCase('get_' . $info->fieldName);
        if (method_exists($this, $getterMethodName)) {
            return $this->{$getterMethodName}($rootValue, $args, $context, $info);
        }
        $execMethodName = $this->camelCase('action_' . $info->fieldName);
        if (method_exists($this, $execMethodName)) {
            return $this->{$execMethodName}($rootValue, $args, $context, $info);
        }
        if (is_object($rootValue) && property_exists($rootValue, $info->fieldName)) {
            return $rootValue->{$info->fieldName};
        }
        if (is_array($rootValue) && array_key_exists($info->fieldName, $rootValue)) {
            return $rootValue[$info->fieldName];
        }

        return $info->returnType;
    }

    protected static function getFieldsByClassNamespace(string $namespace): array
    {
        $fields = [];
        $types = ClassFinder::getClassesInNamespace($namespace);
        foreach ($types as $type) {
            $fieldName = strtolower(substr($type, (strrpos($type, '\\') + 1)));
            $fieldName = str_ireplace(['mutation', 'type'], '', $fieldName);
            $fields[$fieldName] = [
                'type' => Types::get($type),
                'description' => $fieldName,
            ];
        }
        return $fields;
    }

    private function camelCase(string $string): string
    {
        return lcfirst(str_replace(
            ' ',
            '',
            ucwords(preg_replace('/[^a-z0-9]+/', ' ', $string))
        ));
    }
}
