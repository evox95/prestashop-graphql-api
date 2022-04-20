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

namespace PrestaShop\API\GraphQL\Type\Query\Common;

use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Adapter\Entity\Country;

class AddressType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Address',
            'fields' => [
                'id' => Types::id(),
                'alias' => Types::string(),
                'company' => Types::string(),
                'lastname' => Types::string(),
                'firstname' => Types::string(),
                'vat_number' => Types::string(),
                'address1' => Types::string(),
                'address2' => Types::string(),
                'postcode' => Types::string(),
                'city' => Types::string(),
                'other' => Types::string(),
                'phone' => Types::string(),
                'phone_mobile' => Types::string(),
                'id_country' => Types::id(),
                'country_name' => Types::string(),
            ],
        ];
    }

    protected function getCountryName(Address $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return Country::getNameById($context->shopContext->language->id, $objectValue->id_country);
    }
}
