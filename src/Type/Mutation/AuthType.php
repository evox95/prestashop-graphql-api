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
use InvalidArgumentException;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Exception\ApiSafeException;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\CustomerType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\CartRule;
use PrestaShop\PrestaShop\Adapter\Entity\Hook;
use PrestaShopException;

class AuthType extends ObjectType
{
    /**
     * @throws Exception
     */
    protected static function getSchema(): array
    {
        return [
            'name' => 'Auth',
            'fields' => [
                'login' => [
                    'type' => Types::get(CustomerType::class),
                    'description' => 'Login',
                    'args' => [
                        'email' => new NonNull(Types::string()),
                        'password' => new NonNull(Types::string()),
                    ],
                ],
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
     *
     * @return Customer
     *
     * @throws ApiSafeException
     * @throws PrestaShopException
     */
    protected function actionLogin($objectValue, array $args, ApiContext $context, ResolveInfo $info): Customer
    {
        Hook::exec('actionAuthenticationBefore');
        $customer = new Customer();
        try {
            $authentication = $customer->getByEmail($args['email'], $args['password']);
            if ($authentication && (!isset($authentication->active) || $authentication->active)) {
                $context->shopContext->updateCustomer($customer);
                Hook::exec('actionAuthentication', [
                    'customer' => $context->shopContext->customer,
                ]);
                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($context->shopContext);
                CartRule::autoAddToCart($context->shopContext);

                return $context->shopContext->customer;
            }
        } catch (InvalidArgumentException $e) {
        }
        throw new ApiSafeException('Failed to login');
    }
}
