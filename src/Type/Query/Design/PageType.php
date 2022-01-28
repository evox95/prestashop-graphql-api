<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query\Design;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PrestaShop\API\GraphQL\AppContext;
use PrestaShop\API\GraphQL\Model\ObjectType;

class PageType extends ObjectType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'Page',
            'fields' => [
                'id' => Type::id(),
                'content' => [
                    'type' => Type::string(),
                    'description' => '',
                ],
            ],
        ]);
    }
}