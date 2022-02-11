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

namespace PrestaShop\API\GraphQL\Type;

use PrestaShop\API\GraphQL\Model\ObjectType;

class MutationType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Mutation',
            'fields' => self::getFieldsByClassNamespace('PrestaShop\API\GraphQL\Type\Mutation'),
        ];
    }
}
