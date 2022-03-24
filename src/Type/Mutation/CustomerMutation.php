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

namespace PrestaShop\API\GraphQL\Type\Mutation;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Currency;
use PrestaShop\PrestaShop\Adapter\Entity\Language;
use PrestaShop\PrestaShop\Adapter\Entity\Validate;

class CustomerMutation extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'CustomerMutation',
            'fields' => [
                'set_lang' => [
                    'type' => Types::boolean(),
                    'args' => [
                        'lang_iso' => new NonNull(Types::string()),
                    ],
                ],
                'set_currency' => [
                    'type' => Types::boolean(),
                    'args' => [
                        'id_currency' => new NonNull(Types::int()),
                    ],
                ],
            ],
        ];
    }

    protected function actionSetLang($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
    {
        $iso = preg_replace('/[^a-z]+/', '', $args['lang_iso']);
        if (!Validate::isLangIsoCode($iso)) {
            return false;
        }
        $language = new Language((int)Language::getIdByIso($iso));
        if (!Validate::isLoadedObject($language) || !$language->active || !$language->isAssociatedToShop()) {
            return false;
        }

        $result = 1;
        if ($context->shopContext->cookie->id_lang != $language->id) {
            $context->shopContext->cookie->id_lang = $language->id;
            $result &= $context->shopContext->cookie->write();
        }
        if (Validate::isLoadedObject($context->shopContext->customer)) {
            $context->shopContext->customer->id_lang = $language->id;
            $result &= $context->shopContext->customer->update();
        }
        return (bool)$result;
    }

    protected function actionSetCurrency($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
    {
        $idCurrency = (int)$args['id_currency'];
        $currency = new Currency($idCurrency);
        if (!Validate::isLoadedObject($currency) || !$currency->active || !$currency->isAssociatedToShop()) {
            return false;
        }

        $context->shopContext->cookie->id_currency = $idCurrency;
        $context->shopContext->cookie->write();
        return true;
    }
}
