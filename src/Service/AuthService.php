<?php

namespace PrestaShop\API\GraphQL\Service;

use Context;
use Customer;
//use Firebase\JWT\ExpiredException;
//use Firebase\JWT\JWT;
//use Firebase\JWT\Key;
use InvalidArgumentException;
//use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\PrestaShop\Adapter\Entity\CartRule;
use PrestaShop\PrestaShop\Adapter\Entity\Hook;

class AuthService
{

    public static function loginByUsernameAndPassword(string $email, string $password): ?Customer
    {
        Hook::exec('actionAuthenticationBefore');
        $customer = new Customer();
        try {
            $authentication = $customer->getByEmail($email, $password);
            if ($authentication && (!isset($authentication->active) || $authentication->active)) {
                $shopContext = Context::getContext();
                Hook::exec('actionAuthentication', [
                    'customer' => $shopContext->customer,
                ]);
                self::setCustomer(Context::getContext(), $customer);
                return $customer;
            }
        } catch (InvalidArgumentException $e) {
        }
        return null;
    }

//    public static function loginByUsernameAndPassword(string $email, string $password): ?array
//    {
//        Hook::exec('actionAuthenticationBefore');
//        $customer = new Customer();
//        try {
//            $authentication = $customer->getByEmail($email, $password);
//            if ($authentication && (!isset($authentication->active) || $authentication->active)) {
//                $shopContext = Context::getContext();
//                Hook::exec('actionAuthentication', [
//                    'customer' => $shopContext->customer,
//                ]);
//                self::setCustomer(Context::getContext(), $customer);
//                return self::generateTokens([
//                    'id_customer' => $shopContext->customer->id,
//                ]);
//            }
//        } catch (InvalidArgumentException $e) {
//        }
//        return null;
//    }

//    public static function refreshTokens(string $refreshToken): array
//    {
//        try {
//            $decoded = JWT::decode(
//                $refreshToken,
//                new Key(API_GRAPHQL_JWT_REFRESH_SECRET, 'HS256')
//            );
//            return self::generateTokens((array)$decoded->data);
//        } catch (ExpiredException $e) {
//
//        }
//        http_response_code(401);
//        exit();
//    }
//
//    public static function authMiddleware(ApiContext $appContext): void
//    {
//        $token = self::getBearerToken();
//        if (!$token) {
//            return;
//        }
//        try {
//            $decoded = JWT::decode(
//                self::getBearerToken(),
//                new Key(API_GRAPHQL_JWT_ACCESS_SECRET, 'HS256')
//            );
//            $customer = new Customer($decoded->data->id_customer);
//            self::setCustomer($appContext->shopContext, $customer);
//        } catch (ExpiredException $e) {
////            http_response_code(401);
////            exit();
//        }
//    }

    private static function setCustomer(Context $shopContext, Customer $customer): void
    {
        $shopContext->updateCustomer($customer);
        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($shopContext);
        CartRule::autoAddToCart($shopContext);
    }

//    private static function generateTokens($payload): array
//    {
//        return [
//            'accessToken' => JWT::encode(
//                [
//                    'exp' => (time() + API_GRAPHQL_JWT_ACCESS_EXPIRY),
//                    'data' => $payload,
//                ],
//                API_GRAPHQL_JWT_ACCESS_SECRET,
//                'HS256'
//            ),
//            'refreshToken' => JWT::encode(
//                [
//                    'exp' => (time() + API_GRAPHQL_JWT_REFRESH_EXPIRY),
//                    'data' => $payload,
//                ],
//                API_GRAPHQL_JWT_REFRESH_SECRET,
//                'HS256'
//            ),
//        ];
//    }
//
//    private static function getAuthorizationHeader(): ?string
//    {
//        $headers = null;
//        if (isset($_SERVER['Authorization'])) {
//            $headers = trim($_SERVER["Authorization"]);
//        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
//            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
//        } else if (function_exists('apache_request_headers')) {
//            $requestHeaders = apache_request_headers();
//            // Server-side fix for bug in old Android versions
//            $requestHeaders = array_combine(
//                array_map(
//                    'ucwords',
//                    array_keys($requestHeaders)
//                ),
//                array_values($requestHeaders)
//            );
//            if (isset($requestHeaders['Authorization'])) {
//                $headers = trim($requestHeaders['Authorization']);
//            }
//        }
//        return $headers;
//    }
//
//    private static function getBearerToken(): ?string
//    {
//        $headers = self::getAuthorizationHeader();
//        if (!empty($headers)) {
//            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
//                return $matches[1];
//            }
//        }
//        return null;
//    }

}