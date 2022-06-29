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

namespace PrestaShop\API\GraphQL\Type\Query\Sell\Checkout;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class PaymentOptionType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'PaymentOption',
            'fields' => [
                'module_name' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'call_to_action_text' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'additional_information' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'logo' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
                'action' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
            ],
        ];
    }

    protected function getModuleName(PaymentOption $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return $objectValue->getModuleName();
    }

    protected function getCallToActionText(PaymentOption $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return $objectValue->getCallToActionText();
    }

    protected function getAdditionalInformation(PaymentOption $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return $objectValue->getAdditionalInformation();
    }

    protected function getLogo(PaymentOption $objectValue, $args, ApiContext $context, ResolveInfo $info): ?string
    {
        return $objectValue->getLogo();
    }

    protected function getAction(PaymentOption $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return $objectValue->getAction();
    }

}
