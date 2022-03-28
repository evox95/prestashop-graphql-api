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

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\PrestaShop\Adapter\Entity\Configuration;
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

}
