<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Mutation;

use PrestaShop\API\GraphQL\AppContext;
use PrestaShop\API\GraphQL\Data\Customer;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\CustomerType;
use PrestaShop\API\GraphQL\Types;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\PrestaShop\Adapter\Entity\CartRule;
use PrestaShop\PrestaShop\Adapter\Entity\Hook;

class AuthType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Auth',
            'fields' => [
                'login' => [
                    'type' => Types::get(CustomerType::class),
                    'description' => 'Login',
                    'args' => [
                        'email' => new NonNull(Types::string()),
                        'password' => new NonNull(Types::string()),
                    ],
                    'resolve' => fn(...$args): ?\Customer => $this->login(...$args),
                ],
                'logout' => [
                    'type' => Types::boolean(),
                    'description' => 'Logout',
                    'resolve' => fn(...$args): bool => $this->logout(...$args),
                ],
            ],
        ]);
    }

    private function logout($objectValue, array $args, AppContext $context, ResolveInfo $info): bool
    {
        $context->shopContext->customer->mylogout();
        return true;
    }

    private function login($objectValue, array $args, AppContext $context, ResolveInfo $info): \Customer
    {
        Hook::exec('actionAuthenticationBefore');
        $customer = new Customer();
        try {
            $authentication = $customer->getByEmail(
                $args['email'] ?? '',
                $args['password'] ?? ''
            );
            if (!isset($authentication->active) || $authentication->active) {
                $context->shopContext->updateCustomer($customer);
                Hook::exec('actionAuthentication', [
                    'customer' => $context->shopContext->customer
                ]);
                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($context->shopContext);
                CartRule::autoAddToCart($context->shopContext);
            }
        } catch (\InvalidArgumentException $e) {
            // Failed to login
            // @todo Log or something
        }

        return $context->shopContext->customer;
    }

}