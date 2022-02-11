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

namespace PrestaShop\API\GraphQL\Exception;

use GraphQL\Error\ClientAware;

class ApiSafeException extends ApiException implements ClientAware
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'request';
    }
}
