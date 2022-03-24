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

namespace PrestaShop\API\GraphQL;

use Context;

/**
 * Instance available in all GraphQL resolvers as 3rd argument.
 * @property array $productSearchResults
 */
class ApiContext
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
