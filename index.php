<?php declare(strict_types=1);

// Run local test server
// php -S localhost:8080 graphql.php

// Try query
// curl -d '{"query": "query { hello }" }' -H "Content-Type: application/json" http://localhost:8080

require_once __DIR__ . '/../../config/config.inc.php';
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/vendor/autoload.php';

use PrestaShop\Api\AppContext;
use PrestaShop\Api\Type\MutationType;
use PrestaShop\Api\Type\QueryType;
use PrestaShop\Api\Types;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Error\DebugFlag;
use PrestaShop\PrestaShop\Adapter\Entity\Context;

// @todo: Allow enabled front-office token
Configuration::updateValue('PS_TOKEN_ENABLE', false);

//try {
    // See docs on schema options:
    // https://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
    $schema = new Schema([
        'query' => new QueryType(),
        'mutation' => new MutationType(),
        'typeLoader' => static fn(string $name): Type => Types::byTypeName($name),
    ]);

    // Prepare context that will be available in all field resolvers (as 3rd argument):
    $appContext = new AppContext();
    $appContext->shopContext = Context::getContext();; // simulated "currently logged-in user"
    $appContext->rootUrl = Context::getContext()->shop->getBaseURL();
    $appContext->request = $_REQUEST;

    // See docs on server options:
    // https://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
    $server = new StandardServer([
        'schema' => $schema,
        'debugFlag' => _PS_MODE_DEV_
            ? (DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE)
            : DebugFlag::NONE,
        'context' => $appContext,
    ]);

    $server->handleRequest();
//} catch (Throwable $error) {
//    StandardServer::send500Error($error);
//}