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

namespace PrestaShop\API\GraphQL\Type\Query\Module;

use Generator;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Model\ObjectType;
use PrestaShop\API\GraphQL\Type\Query\Module\Ps_Linklist\LinkBlockType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\Module\LinkList\Model\LinkBlock;
use PrestaShop\PrestaShop\Adapter\Entity\Db;

class Ps_LinklistType extends ObjectType
{
    protected static function getSchema(): array
    {
        return [
            'name' => 'Ps_Linklist',
            'fields' => [
                'blocks' => [
                    'type' => new ListOfType(Types::get(LinkBlockType::class)),
                    'description' => '',
                ],
            ],
        ];
    }

    public function getBlocks($rootValue, array $args, ApiContext $context, ResolveInfo $info): Generator
    {
        $sql = 'SELECT ' . LinkBlock::$definition['primary']
            . ' FROM ' . _DB_PREFIX_ . LinkBlock::$definition['table'] . '';
        $blocks = Db::getInstance()->executeS($sql);
        foreach ($blocks as $block) {
            yield (new LinkBlock($block[LinkBlock::$definition['primary']]))->toArray();
        }
    }

}
