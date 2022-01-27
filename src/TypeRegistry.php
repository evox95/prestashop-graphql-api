<?php declare(strict_types=1);

namespace PrestaShop\Api;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;

class TypeRegistry
{
    /**
     * @var array<string, Type>
     */
    private array $types = [];

    /**
     * @var array
     */
    private array $config = [];

    public function __construct()
    {
//        $this->config = require_once
    }

    public function get(string $name): Type
    {
        return $this->types[$name] ??= $this->constructType($name);
    }

    private function constructType(string $name): ObjectType
    {
        return new ObjectType([
            'name' => 'MyTypeA',
            'fields' => fn() => [
                'b' => [
                    'type' => $this->get('MyTypeB')
                ],
            ]
        ]);
    }

}