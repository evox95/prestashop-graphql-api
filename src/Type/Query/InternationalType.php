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

namespace PrestaShop\API\GraphQL\Type\Query;

use Exception;
use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\International\LanguageType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\DbQuery;
use PrestaShop\PrestaShop\Adapter\Entity\Language;
use PrestaShopDatabaseException;
use PrestaShopException;

class InternationalType extends ObjectType
{
    /**
     * @throws Exception
     */
    protected static function getSchema(): array
    {
        return [
            'name' => 'International',
            'fields' => [
                'language' => [
                    'type' => Types::get(LanguageType::class),
                    'description' => 'Returns language by id',
                    'args' => [
                        'id' => new NonNull(Types::id()),
                    ],
                ],
                'languages' => [
                    'type' => new ListOfType(Types::get(LanguageType::class)),
                    'description' => 'Returns subset of languages',
                    'args' => [
                        'offset' => [
                            'type' => Types::int(),
                            'description' => 'Offset',
                            'defaultValue' => 0,
                        ],
                        'limit' => [
                            'type' => Types::int(),
                            'description' => 'Limit',
                            'defaultValue' => 10,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param null $rootValue
     * @param array{id: int} $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     *
     * @return Language
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getLanguage($rootValue, array $args, ApiContext $context, ResolveInfo $info): Language
    {
        $object = new Language((int) $args['id']);

        return $object->active ? $object : new Language();
    }

    /**
     * @param null $rootValue
     * @param array{limit: int, offset: int} $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     *
     * @return Generator<Language>
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getLanguages($rootValue, array $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('a.id_lang');
        $dbQuery->from(Language::$definition['table'], 'a');
        $dbQuery->where('a.active = 1');

        $dbQuery->limit($args['limit'], $args['offset']);

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbQuery);
        foreach ($results as $result) {
            yield new Language($result[Language::$definition['primary']]);
        }
    }
}
