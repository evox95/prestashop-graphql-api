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

namespace PrestaShop\API\GraphQL\Type\Query\Catalog\Listing;

use PrestaShop\API\GraphQL\Type\Query\Catalog\ProductType as ProductTypeCore;

class ProductType extends ProductTypeCore
{
    protected static function getSchema(): array
    {
        return parent::getSchema();
    }
}
