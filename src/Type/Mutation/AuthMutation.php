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

namespace PrestaShop\API\GraphQL\Type\Mutation;

use Customer;
use Exception;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Service\AuthService;
//use PrestaShop\API\GraphQL\Exception\ApiSafeException;
use PrestaShop\API\GraphQL\Model\ObjectType;
//use PrestaShop\API\GraphQL\Type\Mutation\Auth\AuthTokenType;
use PrestaShop\API\GraphQL\Type\Query\CustomerType;
use PrestaShop\API\GraphQL\Types;

class AuthMutation extends ObjectType
{
    /**
     * @throws Exception
     */
    protected static function getSchema(): array
    {
        return [
            'name' => 'AuthMutation',
            'fields' => [
                'login' => [
                    'type' => Types::get(CustomerType::class),
                    'description' => 'Login',
                    'args' => [
                        'email' => new NonNull(Types::string()),
                        'password' => new NonNull(Types::string()),
                    ],
                ],
//                'login' => [
//                    'type' => Types::get(AuthTokenType::class),
//                    'description' => 'Login',
//                    'args' => [
//                        'email' => new NonNull(Types::string()),
//                        'password' => new NonNull(Types::string()),
//                    ],
//                ],
//                'refresh' => [
//                    'type' => Types::get(AuthTokenType::class),
//                    'description' => 'Refresh tokens',
//                    'args' => [
//                        'token' => new NonNull(Types::string()),
//                    ],
//                ],
                'logout' => [
                    'type' => Types::boolean(),
                    'description' => 'Logout',
                ],
            ],
        ];
    }

    protected function actionLogout($objectValue, array $args, ApiContext $context, ResolveInfo $info): bool
    {
        $context->shopContext->customer->mylogout();

        return true;
    }

    /**
     * @param $objectValue
     * @param array{email: string, password: string} $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     * @return Customer|null
     */
    protected function actionLogin($objectValue, array $args, ApiContext $context, ResolveInfo $info): ?Customer
    {
        return AuthService::loginByUsernameAndPassword($args['email'], $args['password']);
    }

//    /**
//     * @param $objectValue
//     * @param array{email: string, password: string} $args
//     * @param ApiContext $context
//     * @param ResolveInfo $info
//     *
//     * @return array
//     *
//     * @throws ApiSafeException
//     */
//    protected function actionLogin($objectValue, array $args, ApiContext $context, ResolveInfo $info): array
//    {
//        $tokens = AuthService::loginByUsernameAndPassword($args['email'], $args['password']);
//        if (!$tokens) {
//            throw new ApiSafeException('Failed to login');
//        }
//        return $tokens;
//    }
//
//    /**
//     * @param $objectValue
//     * @param array{email: string, password: string} $args
//     * @param ApiContext $context
//     * @param ResolveInfo $info
//     *
//     * @return array
//     */
//    protected function actionRefresh($objectValue, array $args, ApiContext $context, ResolveInfo $info): array
//    {
//        return AuthService::refreshTokens($args['token']);
//    }

}
