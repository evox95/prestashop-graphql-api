<?php declare(strict_types=1);

namespace PrestaShop\Api\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends ObjectType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'Category',
            'fields' => [
                'id_category' => Type::id(),
                'name' => Type::string(),
                'id_parent' => Type::id(),
            ],
        ]);
    }

}