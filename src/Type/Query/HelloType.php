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
        return 'Your PrestaShop Front API endpoint is ready! Use a GraphQL client to explore the schema.';
    }
}
