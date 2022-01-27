<?php declare(strict_types=1);

namespace PrestaShop\Api;

/**
 * Instance available in all GraphQL resolvers as 3rd argument.
 */
class AppContext
{
    public string $rootUrl;

    /**
     * @var \Context
     */
    public \Context $shopContext;

    /** @var array<string, mixed> */
    public array $request;
}