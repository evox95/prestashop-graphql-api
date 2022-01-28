<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL;

use Context;

/**
 * Instance available in all GraphQL resolvers as 3rd argument.
 */
class AppContext
{

    /**
     * @var Context
     */
    public Context $shopContext;

    /**
     * @var array<string, mixed>
     */
    public array $request;
}