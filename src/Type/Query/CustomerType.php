<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CustomerType extends ObjectType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'Customer',
            'fields' => [
                'id' => Type::id(),
                'firstname' => Type::string(),
                'lastname' => Type::string(),
            ],
        ]);
    }

}