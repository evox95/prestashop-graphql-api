<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query\Catalog;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends ObjectType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'Category',
            'fields' => [
                'id' => Type::id(),
                'name' => Type::string(),
                'id_parent' => Type::id(),
            ],
        ]);
    }

}