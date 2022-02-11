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

namespace PrestaShop\API\GraphQL\Type\Query\International;

use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\Model\ObjectType;

class LanguageType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Language',
            'fields' => [
                'id' => Type::id(),
                'name' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'iso_code' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'locale' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'language_code' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'date_format_lite' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'date_format_full' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'is_rtl' => [
                    'type' => Type::boolean(),
                    'description' => '',
                ],
            ],
        ];
    }
}
