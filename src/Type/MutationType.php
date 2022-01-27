<?php declare(strict_types=1);

namespace PrestaShop\Api\Type;

use PrestaShop\Api\AppContext;
use PrestaShop\Api\Data\Cart;
use PrestaShop\Api\Data\Customer;
use PrestaShop\Api\Types;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\PrestaShop\Adapter\Entity\CartRule;
use PrestaShop\PrestaShop\Adapter\Entity\CustomerLoginForm;
use PrestaShop\PrestaShop\Adapter\Entity\CustomerLoginFormatter;
use PrestaShop\PrestaShop\Adapter\Entity\Hook;

class MutationType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Mutation',
            'fields' => [
                'login' => [
                    'type' => Types::get(CustomerType::class),
                    'description' => 'Login',
                    'args' => [
                        'email' => new NonNull(Types::string()),
                        'password' => new NonNull(Types::string()),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): ?\Customer {
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
                            // @todo Log or something
                        }

                        return $context->shopContext->customer;
                    }
                ],
                'logout' => [
                    'type' => Types::boolean(),
                    'description' => 'Logout',
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): bool {
                        $context->shopContext->customer->mylogout();
                        return true;
                    }
                ],
                'delete_product_from_cart' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Add product to cart',
                    'args' => [
                        'id_product' => new NonNull(Types::int()),
                        'id_product_attribute' => Types::int(),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): \Cart {
                        $this->cartUpdate($args);
//                        var_dump($errors);die();
                        return Cart::get($objectValue, $args, $context, $info);
                    }
                ],
                'update_product_in_cart' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Add product to cart',
                    'args' => [
                        'id_product' => new NonNull(Types::int()),
                        'id_product_attribute' => Types::int(),
                        'quantity' => new NonNull(Types::int()),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): \Cart {
                        $args['qty'] = $args['quantity'];
                        $args['op'] = $args['quantity'] > 0 ? 'up' : 'down';
                        $args['id_product_attribute'] = $args['id_product_attribute'] ?? 0;
                        $args['update'] = true;
                        $this->cartUpdate($args);
                        return Cart::get($objectValue, $args, $context, $info);
                    }
                ],
                'add_cart_rule' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Add discount code',
                    'args' => [
                        'code' => new NonNull(Types::string()),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): ?\Cart {
                        $args['addDiscount'] = true;
                        $args['discount_name'] = $args['code'];
                        $this->cartUpdate($args);
                        return Cart::get($objectValue, $args, $context, $info);
                    }
                ],
                'delete_cart_rule' => [
                    'type' => Types::get(CartType::class),
                    'description' => 'Delete cart rule',
                    'args' => [
                        'id_cart_rule' => new NonNull(Types::int()),
                    ],
                    'resolve' => function ($objectValue, $args, AppContext $context, ResolveInfo $info): ?\Cart {
                        $args['deleteDiscount'] = $args['id_cart_rule'];
                        $this->cartUpdate($args);
                        return Cart::get($objectValue, $args, $context, $info);
                    }
                ],
            ],
//            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
////                return DataSource::{'find' . ucfirst($name)}(...$arguments);
//                return $this->{$info->fieldName}($rootValue, $args, $context, $info);
//            },
        ]);
    }

    private function cartUpdate(array $args)
    {
        $_GET = $args;
        $_GET['ajax'] = true;
        $cartCtrl = new \CartControllerCore();
        $cartCtrl->init();
        $cartCtrl->postProcess();
//        return $cartCtrl->errors;
    }

    public function deprecatedField(): string
    {
        return 'You can request deprecated field, but it is not displayed in auto-generated documentation by default.';
    }
}