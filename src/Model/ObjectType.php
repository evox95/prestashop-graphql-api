<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Model;

use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\AppContext;

abstract class ObjectType extends \GraphQL\Type\Definition\ObjectType
{

    public function __construct(array $config = [])
    {
        parent::__construct(array_merge([
            'resolveField' => fn(...$args) => $this->resolveField(...$args),
        ], $config));
    }

    /**
     * @param $rootValue
     * @param array $args
     * @param AppContext $context
     * @param ResolveInfo $info
     * @return mixed
     */
    protected function resolveField($rootValue, array $args, AppContext $context, ResolveInfo $info)
    {
        $getterMethodName = $this->camelCase('get_' . $info->fieldName);
        if (method_exists($this, $getterMethodName)) {
            return $this->{$getterMethodName}($rootValue, $args, $context, $info);
        }
        if (is_object($rootValue) && property_exists($rootValue, $info->fieldName)) {
            return $rootValue->{$info->fieldName};
        }
        return $info->returnType;
    }

    private function camelCase(string $string): string
    {
        return lcfirst(str_replace(
            ' ',
            '',
            ucwords(preg_replace('/[^a-z0-9]+/', ' ', $string))
        ));
    }

    public function deprecatedField(): string
    {
        return 'You can request deprecated field, but it is not displayed in auto-generated documentation by default.';
    }

}