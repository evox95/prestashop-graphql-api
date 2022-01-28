<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type;

use PrestaShop\API\GraphQL\Type\Mutation\AuthType;
use PrestaShop\API\GraphQL\Type\Mutation\CartMutation;
use PrestaShop\API\GraphQL\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class MutationType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Mutation',
            'fields' => [
                'auth' => [
                    'type' => Types::get(AuthType::class),
                    'description' => 'Auth',
                    'resolve' => Types::get(AuthType::class),
                ],
                'cart' => [
                    'type' => Types::get(CartMutation::class),
                    'description' => 'Cart',
                    'resolve' => Types::get(CartMutation::class),
                ],
            ],
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($rootValue, $args, $context, $info);
            },
        ]);
    }
}