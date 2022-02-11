Types
---

Types represent data objects, e.g. `ProductType` will return information about Product.

Module allow you to create new or override query/mutation types.

## Override types provided by module

All types provided by this module are located in:
- `{shop_root_dir}/modules/api_graphql/src/Type/Query` retrieves data
- `{shop_root_dir}/modules/api_graphql/src/Type/Mutation` executes some actions like adding product to shopping cart

You can override these types by creating files in:
- `{shop_root_dir}/override/modules/api_graphql/src/Type/Query`
- `{shop_root_dir}/override/modules/api_graphql/src/Type/Mutation`

### Example 

When you run this command
`curl -d '{"query": "query { hello }"}' -H "Content-Type: application/json" http://127.0.0.1/modules/api_graphql/`
you will execute HelloType which will return standard welcome from module
`{"data":{"hello":"Your PrestaShop Front API endpoint is ready! Use a GraphQL client to explore the schema."}}`.

Let's override `{shop_root_dir}/modules/api_graphql/src/Type/Query/HelloType.php` 
by creating file `{shop_root_dir}/override/modules/api_graphql/src/Type/Query/HelloType.php`:
```php
<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query;

use GraphQL\Type\Definition\StringType;

class HelloType extends StringType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'Hello',
        ]);
    }

    public function serialize($value): string
    {
        return 'Your custom welcome message';
    }
}
```

Now when you call again
`curl -d '{"query": "query { hello }"}' -H "Content-Type: application/json" http://127.0.0.1/modules/api_graphql/`
then you will get
`{"data":{"hello":"Your custom welcome message"}}`

## Create new type

You can create new types by creating files in:
- `{shop_root_dir}/override/modules/api_graphql/src/Type/Query`
- `{shop_root_dir}/override/modules/api_graphql/src/Type/Mutation`

### Example

When you try to call `Hello2Type` using
`curl -d '{"query": "query { hello2 }"}' -H "Content-Type: application/json" http://127.0.0.1/modules/api_graphql/`
you will get error message because that type do not exist.

Create file `{shop_root_dir}/override/modules/api_graphql/src/Type/Query/Hello2Type.php`:
```php
<?php declare(strict_types=1);

namespace PrestaShop\API\GraphQL\Type\Query;

use GraphQL\Type\Definition\StringType;

class Hello2Type extends StringType
{

    public function __construct()
    {
        parent::__construct([
            'name' => 'Hello2',
        ]);
    }

    public function serialize($value): string
    {
        return 'Message from your new type Hello2';
    }
}
```

Now when you call again
`curl -d '{"query": "query { hello2 }"}' -H "Content-Type: application/json" http://127.0.0.1/modules/api_graphql/`
then you will get
`{"data":{"hello2":"Message from your new type Hello2"}}`