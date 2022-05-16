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

namespace PrestaShop\API\GraphQL\Type\Query\Sell\Cart;

use Carrier;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;

class DeliveryOptionCarrierType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'DeliveryOptionCarrier',
            'fields' => [
                'id' => ['type' => Type::int()],
                'name' => ['type' => Type::string()],
                'delay' => ['type' => Type::string()],
                'logo_url' => ['type' => Type::string()],
                'price_with_tax' => ['type' => Type::float()],
                'price_without_tax' => ['type' => Type::float()],
                'position' => ['type' => Type::int()],
            ],
        ];
    }

    /**
     * @param array{instance: Carrier} $objectValue
     * @param $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     * @return mixed
     */
    protected function getId(array $objectValue, $args, ApiContext $context, ResolveInfo $info): int
    {
        return (int)$objectValue['instance']->id_reference;
    }

    /**
     * @param array{instance: Carrier} $objectValue
     * @param $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     * @return mixed
     */
    protected function getName(array $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return $objectValue['instance']->name;
    }

    /**
     * @param array{instance: Carrier} $objectValue
     * @param $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     * @return mixed
     */
    protected function getDelay(array $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return $objectValue['instance']->delay;
    }

    /**
     * @param array{logo: string} $objectValue
     * @param $args
     * @param ApiContext $context
     * @param ResolveInfo $info
     * @return mixed
     */
    protected function getLogoUrl(array $objectValue, $args, ApiContext $context, ResolveInfo $info): string
    {
        return $context->shopContext->link->getBaseLink() . $objectValue['logo'];
    }
}
