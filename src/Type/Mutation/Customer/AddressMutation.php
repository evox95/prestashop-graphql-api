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

namespace PrestaShop\API\GraphQL\Type\Mutation\Customer;

use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Adapter\Entity\Validate;

class AddressMutation extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'AddressMutation',
            'fields' => [
                'save' => [
                    'type' => Types::boolean(),
                    'args' => [
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
                        'phone_mobile' => Types::string(),
                    ],
                ],
            ],
        ];
    }

    protected function actionSave($objectValue, $args, ApiContext $context, ResolveInfo $info): bool
    {
        if (isset($args['id'])) {
            $address = new Address((int)$args['id']);
            if (Validate::isLoadedObject($address) && $address->id_customer != $context->shopContext->customer->id) {
                return false;
            }
            unset($args['id']);
        } else {
            $address = new Address();
        }

        foreach ($args as $key => $value) {
            $value = trim($value);
            if (!in_array($key, ['address2', 'phone_mobile', 'company', 'vat_number']) && !$value) {
                return false;
            }
            $address->{$key} = $value;
        }
        return $address->save();
    }

}
