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

namespace PrestaShop\API\GraphQL\Type\Mutation\Auth;

use Exception;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;

class AuthTokenType extends ObjectType
{

    /**
     * @throws Exception
     */
    protected static function getSchema(): array
    {
        return [
            'name' => 'AuthToken',
            'fields' => [
                'accessToken' => [
                    'type' => Types::string(),
                ],
                'refreshToken' => [
                    'type' => Types::string(),
                ],
            ],
        ];
    }

}
