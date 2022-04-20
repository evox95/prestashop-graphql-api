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

use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Common\AddressType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Adapter\Entity\Configuration;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\Language;

class CustomerType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Customer',
            'fields' => [
                'id' => Type::id(),
                'email' => Type::string(),
                'firstname' => Type::string(),
                'lastname' => Type::string(),
                'id_lang' => Type::int(),
                'iso_lang' => Type::string(),
                'id_currency' => Type::int(),
                'addresses' => new ListOfType(Types::get(AddressType::class)),
                'address' => [
                    'type' => Types::get(AddressType::class),
                    'args' => [
                        'id' => new NonNull(Types::id()),
                    ],
                ],
            ],
        ];
    }

    protected function actionIdCurrency($objectValue, $args, ApiContext $context, ResolveInfo $info): int
    {
        return (int)$context->shopContext->cookie->id_currency;
    }

    protected function actionIsoLang($objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return (string)Language::getIsoById($context->shopContext->customer->id_lang)
            ?? Language::getIsoById(Configuration::get('PS_LANG_DEFAULT'));
    }

    protected function getAddresses($objectValue, $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $sql = 'SELECT `id_address` 
            FROM `' . _DB_PREFIX_ . 'address`
            WHERE `id_customer` = ' . (int)$context->shopContext->customer->id . ' AND `deleted` = 0 AND `active` = 1';
        $addresses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($addresses as $address) {
            yield new Address($address['id_address']);
        }
    }

    protected function getAddress($objectValue, $args, ApiContext $context, ResolveInfo $info): ?Address
    {
        $address = new Address($args['id']);
        if ($address->id_customer != $context->shopContext->customer->id) {
            return null;
        }
        return $address;
    }

}
